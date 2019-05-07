<?php
namespace Mf\Users;

use Admin\Service\Zform\RowModelHelper;


return [

        "type" => "izform",
        //"description"=>"",
        "options" => [
            "container" => "profileext",
            "podval" =>"Конфигурация интерфейса должна быть определена в вашем приложении",
            "container-attr"=>"style='width:500px'",
        

            /*внешний вид*/
            "layout"=>[
                "caption" => "Расширеный профиль",
                "rowModel" => [
                    'elements' => [
                        //это ID юзера
                        RowModelHelper::hidden("id"),
                    ],

                ],
            ],
        ],
];