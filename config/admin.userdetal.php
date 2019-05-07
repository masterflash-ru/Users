<?php
namespace Mf\Users;



return [

        "type" => "itabs",
        //"description"=>"",
        "options" => [
            "container" => "userdetal",
            "tabs"=>[
                [
                    "label"=>"Базовый профиль",
                    "interface"=>"users_profile"
                ],
                [
                    "label"=>"Расширенный профиль",
                    "interface"=>"users_profile_ext"
                ],
                [
                    "label"=>"Изменить пароль",
                    "interface"=>"users_password"
                ],

            ],

        ],
];