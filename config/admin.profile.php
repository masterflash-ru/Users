<?php
namespace Mf\Users;

use Admin\Service\Zform\RowModelHelper;



return [

        "type" => "izform",
        "description"=>"Базовый профиль",
        "options" => [
            "container" => "profile",
            "podval" =>"",
            "container-attr"=>"style='width:500px'",
        
            
            /*все что касается чтения в таблицу*/
            "read"=>[
                "db"=>[//плагин выборки из базы
                    "sql"=>"select users.*, (select group_concat(users_group) from users2group where users=users.id) as gr from users where id=".(int)$_GET["id"],  
                ],
            ],

            /*внешний вид*/
            "layout"=>[
                "caption" => "Базовый профиль",
                "rowModel" => [
                    'elements' => [
                        RowModelHelper::text("login"),
                        RowModelHelper::text("name"),
                        RowModelHelper::text("full_name"),
                        RowModelHelper::select("status"),
                        RowModelHelper::submit("submit",['attributes'=>['value' => 'Записать']]),
                    ],

                ],
            ],
        ],
];