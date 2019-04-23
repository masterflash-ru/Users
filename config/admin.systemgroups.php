<?php
namespace Mf\Users;

use Admin\Service\JqGrid\ColModelHelper;
use Admin\Service\JqGrid\NavGridHelper;
use Zend\Json\Expr;



return [
        /*jqgrid - сетка*/
        "type" => "ijqgrid",
        "description"=>"Системные группы",
        "options" => [
            "container" => "sysgroup",
            "podval" =>"<br><b>Редактировать можно только разработчику</b>",
        
            
            /*все что касается чтения в таблицу*/
            "read"=>[
                "db"=>[//плагин выборки из базы
                    "sql"=>"select * from users_group where id<11",
                ],
            ],
            /*внешний вид*/
            "layout"=>[
                "caption" => "Системные группы",
                "height" => "auto",
                "width" => 1000,
                "sortname" => "id",
                "sortorder" => "asc",
                "hidegrid" => false,
                "navgrid" => [
                    "button" => NavGridHelper::Button(["search"=>false,"add"=>false,"edit"=>false,"del"=>false]),
                ],
                "colModel" => [
                    ColModelHelper::text("id",["label"=>"ID"]),
                    ColModelHelper::text("description",["label"=>"Описание","width"=>400]),
                    ColModelHelper::text("name",["label"=>"Имя","width"=>400]),
                ],
            ],
        ],
];