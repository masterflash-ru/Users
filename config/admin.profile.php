<?php
namespace Mf\Users;

use Admin\Service\Zform\RowModelHelper;



return [

        "type" => "izform",
        //"description"=>"",
        "options" => [
            "container" => "profile",
            "podval" =>"",
            "container-attr"=>"style='width:500px'",
        
            
            /*все что касается чтения в таблицу*/
            "read"=>[
                "db"=>[//плагин выборки из базы
                    "sql"=>"select users.*, 
                        (select group_concat(users_group) from users2group where users=users.id) as gr 
                            from users where id=:id",  
                ],
            ],

            /*внешний вид*/
            "layout"=>[
                "caption" => "Базовый профиль",
                "rowModel" => [
                    'elements' => [
                        RowModelHelper::text("login",['options'=>["label"=>"Логин"]]),
                        RowModelHelper::text("name",['options'=>["label"=>"Имя"]]),
                        RowModelHelper::text("full_name",['options'=>["label"=>"Полное имя"]]),
                        RowModelHelper::select("status",[
                            "plugins"=>[
                                "rowModel"=>[//плагин срабатывает при генерации формы до ее вывода
                                    "GetUserStatus"=>[],
                                ],
                            ],
                            'options'=>[
                                "label"=>"Статус"
                            ],
                        ]),
                        RowModelHelper::datetime("date_registration",['options'=>["label"=>"Дата регистрации"]]),
                        RowModelHelper::select("gr",[
                            "plugins"=>[
                                "rowModel"=>[//плагин срабатывает при генерации формы до ее вывода
                                    "selectfromdb"=>[
                                        "sql"=>"select id,name from users_group order by name",
                                        "emptyFirstItem"=>false
                                    ],
                                ],
                            ],
                            'attributes' => [
                                "multiple"=>true,
                                "size"=>7,
                            ],
                            'options'=>[
                                "label"=>"Член групп",
                                
                            ],
                        ]),
                        RowModelHelper::submit("submit",[
                            'attributes'=>['value' => 'Записать'],
                        ]),
                        RowModelHelper::hidden("id"),
                    ],

                ],
            ],
        ],
];