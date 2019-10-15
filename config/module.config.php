<?php
namespace Mf\Users;
use Zend\Authentication\AuthenticationService;

if (empty($_SERVER["SERVER_NAME"])){
    //скорей всего запустили из консоли
    $_SERVER["SERVER_NAME"]="localhost";
    $_SERVER["REQUEST_SCHEME"]="http";
}

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
    "permission"=>[
        "objects" =>[
            "interface/systemgroups" =>  [1,1,0740],
            "interface/usergroups" =>    [1,1,0740],
            "interface/users" =>         [1,1,0760],
            "interface/users_profile" => [1,1,0760],
            "interface/users_password" =>[1,1,0760],
        ],
    ],

    "users" => [
        /*базовый список допустимых состояний регистрированных юзеров, ключ - это код состояния*/
        'users_status' => [
            0=>"Состояние не определено",
            3=>"Нормальное состояние",
            4=>"Заблокирован",
            -1=>"Удален"
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
    "email_robot"=>"robot@".trim((isset($_SERVER["SERVER_NAME"])) ? $_SERVER["SERVER_NAME"]:"localhost","w."),
    /*Канонический адрес сайта*/
    "ServerDefaultUri"=>$_SERVER["REQUEST_SCHEME"]."://".trim($_SERVER["SERVER_NAME"],"w."),
    
    /*плагины для сетки JqGrid*/
    "JqGridPlugin"=>[
        'factories' => [
            Service\Admin\JqGrid\Plugin\SaveGroupTree::class=>Service\Admin\JqGrid\Plugin\FactorySaveGroupTree::class,
            Service\Admin\JqGrid\Plugin\SaveUser::class=>Service\Admin\JqGrid\Plugin\FactorySaveUser::class,
            Service\Admin\JqGrid\Plugin\UserStatus::class=>Service\Admin\JqGrid\Plugin\FactoryUserStatus::class,
        ],
        'aliases' =>[
            "SaveGroupTree" => Service\Admin\JqGrid\Plugin\SaveGroupTree::class,
            "savegrouptree" => Service\Admin\JqGrid\Plugin\SaveGroupTree::class,
            "SaveUser" => Service\Admin\JqGrid\Plugin\SaveUser::class,
            "UserStatus" => Service\Admin\JqGrid\Plugin\UserStatus::class,
        ],
    ],
    /*плагины для Zform*/
    "ZformPlugin"=>[
        'factories' => [
            Service\Admin\Zform\Plugin\GetUserStatus::class=>Service\Admin\Zform\Plugin\FactoryGetUserStatus::class,
            Service\Admin\Zform\Plugin\EditUserProfile::class=>Service\Admin\Zform\Plugin\FactoryEditUserProfile::class,
            Service\Admin\Zform\Plugin\EditUserPassword::class=>Service\Admin\Zform\Plugin\FactoryEditUserProfile::class,
        ],
        'aliases' =>[
            "GetUserStatus" => Service\Admin\Zform\Plugin\GetUserStatus::class,
            "EditUserProfile" => Service\Admin\Zform\Plugin\EditUserProfile::class,
            "EditUserPassword" => Service\Admin\Zform\Plugin\EditUserPassword::class,
        ],
    ],
    /*описатели интерфейсов*/
    "interface"=>[
        "systemgroups"=>__DIR__."/admin.systemgroups.php",
        "usergroups"=>__DIR__."/admin.usergroups.php",
        "users"=>__DIR__."/admin.users.php",
        "users_profile"=>__DIR__."/admin.profile.php",
        "users_profile_ext"=>__DIR__."/admin.profileext.php",
        "users_password"=>__DIR__."/admin.password.php",
        "usersdetal"=>__DIR__."/admin.userdetal.php",
    ]

];
