<?php
/**
*плагин 
*/

namespace Mf\Users\View\Helper;



use Zend\View\Helper\AbstractHelper;


/**
 * 
 */
class User extends AbstractHelper 
{
    /**
    * экземпляр сервиса Mf\Users\Service\User
    */
    protected $User;
    
    public function __construct($User)
    {
        $this->User=$User;

    }

    /**
    * просто возвращает экземпляр данного объекта
    * уже программа должна будет обращаться к внутренностям
    */
    public function __invoke()
    {
        return $this;
    }
    
    /*повторяет метод identity сервиса Users
    * если юзер авторизован, возвращается его ID, если нет то ничего
    */
    public function identity()
    {
        return $this->User->identity();
    }

    /*
    *возвращает ID авторизованного юзера
    */
    public function getUserId()
    {
        return $this->User->getUserId();
    }
}