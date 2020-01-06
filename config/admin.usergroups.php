<?php
namespace Mf\Users;

use Admin\Service\JqGrid\ColModelHelper;
use Admin\Service\JqGrid\NavGridHelper;
use Laminas\Json\Expr;



return [
        /*jqgrid - сетка*/
        "type" => "ijqgrid",
        "description"=>"Группы пользователей",
        "options" => [
            "container" => "sysgroup",
            "podval" =>"<br><b>Не системные группы пользователей, которые можно редактировать</b>",
        
            
            /*все что касается чтения в таблицу*/
            "read"=>[
                "db"=>[//плагин выборки из базы
                    "sql"=>"select users_group.*, (select group_concat(parent_id) from users_group_tree where users_group.id=users_group_tree.id) as parent_group from users_group where id>10",
                ],
            ],
             "edit"=>[
                 "SaveGroupTree"=>[]
             ],
             "add"=>[
                 "SaveGroupTree"=>[]
             ],
             "del"=>[
                 "SaveGroupTree"=>[]
             ],

            /*внешний вид*/
            "layout"=>[
                "caption" => "Группы пользователей",
                "height" => "auto",
                "width" => 1000,
                "sortname" => "name",
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
                    ColModelHelper::text("name",
                                         [
                                             "label"=>"Имя",
                                             "width"=>400,
                                             "editoptions" => [
                                                 "size" => 80,
                                             ],
                                         ]),
                    ColModelHelper::text("description",
                                         [
                                             "label"=>"Описание",
                                             "width"=>400,
                                             "editoptions" => [
                                                 "size" => 80,
                                             ],
                                         ]),

                    ColModelHelper::select("parent_group",["label"=>"Является членом групп",
                                                           "width"=>400,
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
                    ColModelHelper::cellActions(),
                ],
            ],
        ],
];