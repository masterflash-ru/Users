<?php
namespace Mf\Users;
use Zend\Authentication\AuthenticationService;

return [
    'service_manager' => [
        'factories' => [
            /*инициализация Zend\Authentication\AuthenticationService - в фабрике передается адаптер аутентификации и хранилище*/
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
        ],
        'factories' => [
            Controller\Plugin\User::class => Controller\Plugin\UserFactory::class,
        ],
    ],
    'view_helpers' => [
        'factories' => [
            View\Helper\User::class => View\Helper\UserFactory::class,
        ],
        'aliases' => [
            'User' => View\Helper\User::class,
            'user' => View\Helper\User::class,
        ],
    ],

    "users" => [
        /*базовый список допустимых состояний регистрированных юзеров, ключ - это код состояния*/
        'users_status' => [
            0=>"Состояние не определено",
            3=>"Нормальное состояние",
            4=>"Заблокирован",
        ],
        /*код состояния при начальной регистрации нового посетителя*/
        "users_status_start_registration" => 1,
        /*Группа к которой привязывается новый посетитель*/
        "users_groups_start_registration"=>[7],
        /*новый код состояния после подтверждения регистрации*/
        "users_status_after_confirm" => 3,
        /*статусы когда юзер может в принципе авторизоваться*/
        "users_status_login" => [3],
    ],
    //обратный адрес
    "email_robot"=>"robot@".trim($_SERVER["SERVER_NAME"],"w."),
    /*Канонический адрес сайта*/
    "ServerDefaultUri"=>"http://".trim($_SERVER["SERVER_NAME"],"w."),
    
    /*плагины для сетки JqGrid*/
    "JqGridPlugin"=>[
        'factories' => [
            Service\Admin\JqGrid\Plugin\SaveGroupTree::class=>Service\Admin\JqGrid\Plugin\FactorySaveGroupTree::class,
            Service\Admin\JqGrid\Plugin\ReadGroup::class=>Service\Admin\JqGrid\Plugin\FactoryReadGroup::class,
        ],
        'aliases' =>[
            "SaveGroupTree" => Service\Admin\JqGrid\Plugin\SaveGroupTree::class,
            "savegrouptree" => Service\Admin\JqGrid\Plugin\SaveGroupTree::class,
            "ReadGroup" => Service\Admin\JqGrid\Plugin\ReadGroup::class,
        ],
    ],
    /*описатели интерфейсов*/
    "interface"=>[
        "systemgroups"=>__DIR__."/admin.systemgroups.php",
        "usergroups"=>__DIR__."/admin.usergroups.php",
    ]

];
