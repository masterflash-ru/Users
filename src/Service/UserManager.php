<?php
namespace Mf\Users\Service;

use Mf\Users\Entity\Users;

use Zend\Crypt\Password\Bcrypt;
use Zend\Math\Rand;
use Mf\Users\Exception;
use ADO\Service\RecordSet;
use ADO\Service\Command;


/**
 * сервис для управления юзерами, объект не привязан ни к какому юзеру в данный момент
 * получается что бы управлять юзерами сюда нужно передавать ID юзера с которым работаем
 * использует 2 таблицы users и users_ext - поля таблиц считываются в конструкторе и используются для автоматического распределения данных
 */
class UserManager
{
    /**
     * соединение с базой
     */
    protected $connection;
    
    /**
    * кеш
    */
    protected $cache;
    /*
    *массив имен колонок в базовой таблице юзеров
    */
    protected $db_field_base=[];

    /*
    *массив имен колонок в расширеной  таблице юзеров
    *имена полей зависит от приложения и их имена записываются в конструкторе
    *первичный ключ id считается железно
    */
    protected $db_field_ext=[];
    
    /*
    * время жизни временного пароля в сек.
    */
    protected $passwordLifetime=86400;
    
    /*
    * конфиг, секция users
    */
    protected $config;

    /**
     * Constructs the service.
     */
    public function __construct($connection,$cache,$config) 
    {
        $this->connection = $connection;
        $this->cache=$cache;
        $this->config=$config["users"];
        
        $key="users_tables_structure";
        //пытаемся считать из кеша
        $result = false;
        $users_tables= $this->cache->getItem($key, $result);
        if (!$result ) {
            $users_tables=[];
            //промах кеша, создаем
            $rs=new RecordSet();
            $rs->Open("show columns from users",$this->connection);
            while (!$rs->EOF){
                $users_tables[0][]=$rs->Fields->Item["Field"]->Value;
                $rs->MoveNext();
            }
            $rs->Close();
            $rs=new RecordSet();
            $rs->Open("show columns from users_ext",$this->connection);
            while (!$rs->EOF){
                $users_tables[1][]=$rs->Fields->Item["Field"]->Value;
                $rs->MoveNext();
            }
            $rs->Close();

            //сохраним в кеш
            $this->cache->setItem($key, $users_tables);
        }
        $this->db_field_base=$users_tables[0];
        $this->db_field_ext=$users_tables[1];

    }
    
    /**
     * добавить нового юзера
     *на входе массив ключи которого это имена колонок
     *в какую таблицу писать работает автоматически
     *возвращается экземпляр Mf\Users\Entity\Users с заполнеными данными
     */
    public function addUser($data) 
    {
        if(empty($data['login'])) {
            throw new Exception\MissingParameterException("Нет обязательного параметра login, добавить нового юзера нельзя");
        }
        if($this->isUserExists($data['login'])) {
            throw new Exception\AlreadyExistException("Пользователь с логином " . $data['login'] . " уже зарегистрирован");
        }
        if (empty($data["status"])){/*если не указали группу, берем из конфига*/
            $data["status"]=(int)$this->config["users_status_start_registration"];
        }
        /*дата регистрации*/
         $data["date_registration"]=date("Y-m-d H:i:s");
        $data["confirm_hash"]=md5(Rand::getString(20, '0123456789abcdefghijklmnopqrstuvwxyz', true));
        $rez=$this->_updateUserInfo(0, $data,true);
        /*присвоим группу*/
        $this->setGroupIds($rez->getId(),$this->config["users_groups_start_registration"]);
        return $rez;
    }
    
    /*
    *получить инфу по юзеру c id
    *возвращает users  (экземпляр объекта)
    */
    public function GetUserIdInfo($id)
    {
        $id=(int)$id;
        $key="users_{$id}";
        //пытаемся считать из кеша
        $result = false;
        $user= $this->cache->getItem($key, $result);
        if (!$result) {
            //читаем и заполняем сущность "юзер"
            $this->connection->BeginTrans();
            $rs=$this->connection->Execute("select * from users u,users_ext e where u.id=e.id and u.id=".(int)$id);
            $this->connection->CommitTrans();

            if ($rs->EOF){
                throw new Exception\NotFoundException("Юзера с id={$id} не существует");
            }
            $user=$rs->FetchEntity(Users::class);
            $rs->Close();
            //сохраним в кеш
            $this->cache->setItem($key, $user);
        }
        return $user;
    }

    /**
     * Обновление инфы в профиле юзера, автоматом пишется в основную или дополнительную таблицы.
     * $userid = ID юзера длья которог оменяем инфу
     * если ошибка - исключение
     * возвращает экземпляр users
     */
    public function updateUserInfo ($userid, $data) 
    {
        $userid=(int)$userid;
        $this->cache->removeItem("users_{$userid}");
        return $this->_updateUserInfo($userid, $data);
    }

    /**
     * проверяет наличие юзера с указаным $confirm хешем в базе.     
     *возвращает  users  (экземпляр объекта), если не существует - исключение
     * обновляет статус посетителя
     */
    public function userConfirm($confirm) 
    {
        $c=new Command();
        $c->NamedParameters=true;
        $c->ActiveConnection=$this->connection;
        $p=$c->CreateParameter('confirm', adChar, adParamInput, 127, $confirm);//генерируем объек параметров
        $c->Parameters->Append($p);//добавим в коллекцию
        $c->CommandText="select id,confirm_hash,status from users where confirm_hash=:confirm";

        $rs=new RecordSet();
        $rs->CursorType = adOpenKeyset;
        $rs->Open($c);
        if ($rs->EOF){
            throw new Exception\NotFoundException("Юзера с confirm_hash={$confirm} не существует");
        }
        /*обновим статус и удалим строку хеша*/
        $rs->Fields->Item["confirm_hash"]->Value=null;
        $rs->Fields->Item["status"]->Value=(int)$this->config["users_status_after_confirm"];
        $rs->Update();
        return $this->GetUserIdInfo((int)$rs->Fields->Item["id"]->Value);
    }
    
    
    /**
     * проверяет наличие юзера с указаным логином в базе.     
     *возвращает true - есть в базе, false - нет
     */
    public function isUserExists($login) 
    {
        $c=new Command();
        $c->NamedParameters=true;
        $c->ActiveConnection=$this->connection;
        $p=$c->CreateParameter('login', adChar, adParamInput, 127, $login);//генерируем объек параметров
        $c->Parameters->Append($p);//добавим в коллекцию
        $c->CommandText="select id from users where login=:login";

        $rs=new RecordSet();
        $rs->Open($c);

        return !$rs->EOF;
    }
    
    /**
    * получить массив ID групп, которым принадлежит юзер с id
    * user_id - ID юзера
    * возвращается массив всегда, если юзер не найден - исключение
    */
    public function getGroupIds($user_id)
    {
        $user_id=(int)$user_id;
        $key="group_users_{$user_id}";
        //пытаемся считать из кеша
        $result = false;
        $group= $this->cache->getItem($key, $result);
        if (!$result) {
            //читаем и заполняем сущность "юзер"
            $this->connection->BeginTrans();
            $rs=$this->connection->Execute("select users_group from users2group  where users={$user_id}
                                            union
                                            select ugt.parent_id as users_group from users_group_tree as ugt,users2group ug 
                                                where ugt.id=ug.users_group and ug.users={$user_id}");
            $this->connection->CommitTrans();
            $group=[];
            while (!$rs->EOF){
                $group[]=(int)$rs->Fields->Item["users_group"]->Value;
                $rs->MoveNext();
            }
            $group=array_unique($group);
            
            $rs->Close();
            //сохраним в кеш
            $this->cache->setItem($key, $group);
        }
        return $group;
    
    }

    /**
    *привязать юзера к группам, старые связи будут удалены
    * $user_id - int - ID юзера
    * $group_ids - массив ID групп, к которым привяжется юзер
    */
    public function setGroupIds($user_id,array $group_ids)
    {
        $user_id=(int)$user_id;
        $this->connection->BeginTrans();
        //удалим старые связи
        $a=0;
        $this->connection->Execute("delete from users2group where users={$user_id}",$a,adExecuteNoCreateRecordSet);
        $rs11=new RecordSet();
        $rs11->CursorType = adOpenKeyset;
        $rs11->open("SELECT * FROM users2group where users={$user_id}",$this->connection);
        
        foreach ($group_ids as $gr){
            $rs11->AddNew();
            $rs11->Fields->Item['users']->Value=$user_id;
            $rs11->Fields->Item['users_group']->Value=(int)$gr;
            $rs11->Update();
        }
        $this->connection->CommitTrans();
        $this->cache->removeItem("group_users_{$user_id}");
    }
    
    /**
     * генерация уникальной случайной строки для генерации адреса и пишет во временный пароль
     * там же пишется время жизни этого пароля
     * возвращает строку временного сгеренированног пароля
     */
    public function PasswordReset(string $login,$passwordLen=10)
    {
        $c=new Command();
        $c->NamedParameters=true;
        $c->ActiveConnection=$this->connection;
        $p=$c->CreateParameter('login', adChar, adParamInput, 127, $login);//генерируем объек параметров
        $c->Parameters->Append($p);//добавим в коллекцию
        $c->CommandText="select id,temp_password,temp_date,login from users where login=:login";

        $rs=new RecordSet();
        $rs->CursorType =adOpenKeyset;
        $rs->Open($c);
        if($rs->EOF) {
            throw new Exception\NotFoundException("Пользователя с логином " . $login . " нет");
        }
        $pass=Rand::getString($passwordLen, '0123456789!@#%$abcdefghijklmnopqrstuvwxyz', true);
        $rs->Fields->Item["temp_password"]->Value =$pass;
        $rs->Fields->Item["temp_date"]->Value  = date('Y-m-d H:i:s',time()+$this->passwordLifetime);
        $rs->Update();
        return $pass;
    }
    
    
    /**
     * Обновление инфы/создание в профиле юзера, автоматом пишется в основную или дополнительную таблицы.
     * $userid = ID юзера длья которог оменяем инфу
     * $flag_create_new - если true, если нет юзера, создается новая запись
     * если ошибка - исключение
     * возвращает экземпляр users
     */
    protected function _updateUserInfo ($userid=0, array $data=[],$flag_create_new=false) 
    {

        $userid=(int)$userid;
        $rs=new RecordSet();
        $rs->CursorType =adOpenKeyset;
        $rs->Open("select * from users where id=$userid",$this->connection);
        if($rs->EOF && !$flag_create_new) {
            throw new Exception\NotFoundException("Юзера с id={$userid} не найдено");
        }
        if($rs->EOF && $flag_create_new) {
            $rs->AddNew();
        }

        
        
        if (isset($data['password'])){
            // шифруем пароль
            $bcrypt = new Bcrypt();
            $data['password'] = $bcrypt->create($data['password']);
        }
        if (isset($data['temp_password'])){
            // шифруем пароль
            $bcrypt = new Bcrypt();
            $data['temp_password'] = $bcrypt->create($data['temp_password']);
        }
        
        //пробежим по базовой таблице
        foreach ($this->db_field_base as $field){
            if (array_key_exists($field,$data)){
                $rs->Fields->Item[$field]->Value=$data[$field];
            }
        }
        $this->connection->BeginTrans();
        //запишем в базовую таблицу информацию и получим ID нового юзера
        $rs->Update();
        $this->connection->CommitTrans();
        
        
        $this->connection->BeginTrans();
        $rs_ext=new RecordSet();
        $rs_ext->CursorType =adOpenKeyset;
        $rs_ext->Open("select * from users_ext where id=$userid",$this->connection);
        if($rs_ext->EOF && $flag_create_new) {
            $rs_ext->AddNew();
            $userid=(int)$rs->Fields->Item["id"]->Value;
            $data["id"]=$userid;
        }

        //пробежим по расширеной таблице
        foreach ($this->db_field_ext as $field){
            if (array_key_exists($field,$data)){
                $rs_ext->Fields->Item[$field]->Value=$data[$field];
            }
        }
        $rs_ext->Update();
        $this->connection->CommitTrans();
        $rs_ext->Close();
        $rs->Close();
        return $this->GetUserIdInfo($userid);
    }

}

