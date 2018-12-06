<?php
namespace Mf\Users;
use Zend\Authentication\AuthenticationService;

return [
    'service_manager' => [
        'factories' => [//сервисы-фабрики
            AuthenticationService::class => Service\Factory\AuthenticationServiceFactory::class,
            Service\AuthAdapter::class => Service\Factory\AuthAdapterFactory::class,
            Service\AuthManager::class => Service\Factory\AuthManagerFactory::class,
            Service\UserManager::class => Service\Factory\UserManagerFactory::class,
            Service\User::class => Service\Factory\UserFactory::class,
        ],
        'aliases'=>[
            "user"=>Service\UserManager::class,
            "User"=>Service\UserManager::class,
        ],
    ],
    /*помощник в контроллеры для проверки доступа и для работы с авторизованным юзером*/
    'controller_plugins' => [
        'aliases' => [
            'user' => Controller\Plugin\User::class,
            'User' => Controller\Plugin\User::class,
            'Zend\Mvc\Controller\Plugin\User' => Controller\Plugin\User::class,
        ],
        'factories' => [
            Controller\Plugin\User::class => Controller\Plugin\UserFactory::class,
        ],
    ],
    "users" => [
        /*список допустимых состояний регистрированных юзеров, ключ - это код состояния*/
        'users_status' => [
            0=>"Неопределенное",
            1=>"Неподтвержденная регистрация",
            2=>"Ожидает подтверждения администрации",
            3=>"Нормальное состояние",
            4=>"Заблокирован",
        ],
        /*код состояния при начальной регистрации нового посетителя*/
        "users_status_start_registration" => 1,
        /*новый код состояния после подтверждения регистрации*/
        "users_status_after_confirm" => 3,
        /*нормальное состояние посетителя, когда он может делать все*/
        "users_status_normal" => 3,
    ],
  //обратный адрес
  "email_robot"=>"robot@".trim($_SERVER["SERVER_NAME"],"w."),

];
