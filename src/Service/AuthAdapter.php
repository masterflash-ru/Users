<?php
/*
* Объект который собственно производит авторизацию, 
* возвращает экземпляр  Laminas\Authentication\Result с результатом аутентификации
*/
namespace Mf\Users\Service;


use Laminas\Authentication\Adapter\AdapterInterface;
use Laminas\Authentication\Result;
use Laminas\Crypt\Password\Bcrypt;
use Mf\Users\Entity\Users;
use Laminas\Authentication\Adapter\AbstractAdapter;

use ADO\Service\RecordSet;
use ADO\Service\Command;



/**
адаптер аутентификации
 */
class AuthAdapter extends AbstractAdapter
{

    protected $config;

/**
*соединение с базой
   */
    private $connection;


    public function __construct($connection,$config)
    {
        $this->connection = $connection;
        $this->config=$config;
    }


    /**
     * Performs an authentication attempt.
     */
    public function authenticate()
    {
        $users_status_login=" status in(".implode(",",$this->config["users"]["users_status_login"]).")";
        $c=new Command();
        $c->NamedParameters=true;
        $c->ActiveConnection=$this->connection;
        $p=$c->CreateParameter('login', adChar, adParamInput, 50, $this->identity);//генерируем объек параметров
        $c->Parameters->Append($p);//добавим в коллекцию
        $c->CommandText="select * from users where login=:login and {$users_status_login}";

      $rs=new RecordSet();
      $rs->CursorType =adOpenKeyset;
      $rs->Open($c);
        
       $users= $rs->FetchEntity(Users::class);

        // If there is no such user, return 'Identity Not Found' status.
        if ($users == null) {
            return new Result(
                Result::FAILURE_IDENTITY_NOT_FOUND, 
                null, 
                ['Пользователь не найден']);
        }

        $bcrypt = new Bcrypt();

        if ($bcrypt->verify($this->credential, $users->getPassword())) {
            //успешная авторизация, возвращаем успех и ID записи из таблицы админов
            return new Result(
                    Result::SUCCESS, 
                    $users->getId(), 
                    ['Аутентификация успешна']);
        }
        
        /*проверяем по временному паролю, для варианта восстановления пароля*/

        /*смотрим дату, годен ли временный пароль*/
        if ($users->getTemp_Date()){
            /*собственно пароль*/
            if ($bcrypt->verify($this->credential, $users->getTemp_Password()) && strtotime($users->getTemp_Date())-time() >0) {
                //успешная авторизация, возвращаем успех и ID записи из таблицы админов
                return new Result(
                        Result::SUCCESS, 
                        $users->getId(), 
                        ['Аутентификация успешна по временному паролю']);
            }
        }

        return new Result(
                Result::FAILURE_CREDENTIAL_INVALID, 
                null, 
                ['Неверный пароль или иная ошибка']);
    }

}
