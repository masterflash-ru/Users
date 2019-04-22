<?php
namespace Admin;

use Admin\Service\JqGrid\ColModelHelper;
use Admin\Service\JqGrid\NavGridHelper;
use Zend\Json\Expr;



return [
        /*jqgrid - сетка*/
        "type" => "ijqgrid",
        "description"=>"Все пользователи",
        "options" => [
            "container" => "usersall",
            "podval" =>"",
        
            
            /*все что касается чтения в таблицу*/
            "read"=>[
                "db"=>[//плагин выборки из базы
                    "sql"=>"select users.*,
                        (select group_concat(ug.users_group) from users2group ug where ug.users=users.id) as user_groups
                                from users",
                ],
            ],
             "edit1"=>[
                 "SaveGroupTree"=>[]
             ],
             "add1"=>[
                 "SaveGroupTree"=>[]
             ],
             "del1"=>[
                 "SaveGroupTree"=>[]
             ],

            /*внешний вид*/
            "layout"=>[
                "caption" => "Все пользователи",
                "height" => "auto",
                //"width" => 1000,
                "sortname" => "id",
                "sortorder" => "asc",
                "hidegrid" => false,
                "navgrid" => [
                    "button" => NavGridHelper::Button(["search"=>false,"add"=>true,"edit"=>true,"del"=>true]),
                    "editOptions"=>NavGridHelper::editOptions(),
                    "addOptions"=>NavGridHelper::addOptions(),
                    "delOptions"=>NavGridHelper::delOptions(),

                ],
                "colModel" => [
                    ColModelHelper::text("id",["label"=>"ID","width"=>80,"editable"=>false]),
                    ColModelHelper::text("login",
                                         [
                                             "label"=>"Логин",
                                             "width"=>110,
                                             "editoptions" => [
                                                 "size" => 80,
                                             ],
                                         ]),

                    ColModelHelper::text("name",
                                         [
                                             "label"=>"Имя",
                                             "width"=>150,
                                             "editoptions" => [
                                                 "size" => 80,
                                             ],
                                         ]),
                    ColModelHelper::text("full_name",
                                         [
                                             "label"=>"Полное имя",
                                             "width"=>150,
                                             "editoptions" => [
                                                 "size" => 80,
                                             ],
                                         ]),
                    ColModelHelper::text("date_registration",
                                         [
                                             "label"=>"Дата регистрации",
                                             "width"=>150,
                                             "editable"=>false,
                                             "formatter" => "datetime",
                                         ]),
                    ColModelHelper::text("date_last_login",
                                         [
                                             "label"=>"Дата посл. входа",
                                             "width"=>150,
                                             "editable"=>false,
                                             "formatter" => "datetime",
                                         ]),

                    ColModelHelper::select("user_groups",["label"=>"Является членом групп",
                                                           "width"=>200,
                                                           "editoptions"=>[
                                                               "multiple"=>true,
                                                               "size"=>10,
                                                           ],
                                                           "plugins"=>[
                                                               "colModel"=>[//плагин срабатывает при генерации сетки, вызывается в помощнике сетки
                                                                   "selectfromdb"=>[
                                                                       "sql"=>"select id,name from users_group order by name",
                                                                       "emptyFirstItem"=>true
                                                                   ],
                                                               ],
                                                           ],
                                                          ]),

                    ColModelHelper::interfaces("id",
                                         [
                                             "label"=>"Перейти",
                                             "width"=>200,
                                             "formatoptions" => [
                                                 "items"=>[
                                                    "button1"=> [
                                                        "label"=>"Базовый профиль",
                                                        "interface"=>"/adm/universal-interface/usergroups",
                                                        "icon"=> "ui-icon-contact ui-priority-primary",
                                                        "dialog"=>[
                                                            "title"=>"Базовый профиль",
                                                        ],
                                                     ],
                                                    "button2"=> [
                                                        "label"=>"Сменить пароль",
                                                        "interface"=>"/adm/universal-interface/usergroups",
                                                        "icon"=> "ui-icon-locked",
                                                        "dialog"=>[
                                                            "title"=>"Сменить пароль",
                                                        ],

                                                     ],
                                                 ],
                                             ]
                                         ]),

                    ColModelHelper::cellActions(),
                ],
            ],
        ],
];