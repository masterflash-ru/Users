<?php
namespace Mf\Users;

use Admin\Service\Zform\RowModelHelper;


return [

        "type" => "izform",
        //"description"=>"",
        "options" => [
            "container" => "password",
            "podval" =>"",
            "container-attr"=>"style='width:300px'",
        
            
            /*все что касается чтения в таблицу*/
            "edit1"=>[
                "EditUserProfile"=>[],
            ],
            
            /*поведение формы*/
            "actionsEvent"=>[
                /*что делать после успешной записи формы*/
                "FormAfterSubmitOkEvent"=>'setTimeout(function(){$("#interfacesDialog").dialog("close");},500);',
            ],

            /*внешний вид*/
            "layout"=>[
                "caption" => "Изменить пароль",
                "rowModel" => [
                    'elements' => [
                        RowModelHelper::text("password",['options'=>["label"=>"Пароль"]]),
                        RowModelHelper::text("password1",['options'=>["label"=>"Повторите пароль"]]),
                        RowModelHelper::submit("submit",[
                            'attributes'=>['value' => 'Записать'],
                        ]),

                        //это ID юзера
                        RowModelHelper::hidden("id"),
                    ],

                ],
            ],
        ],
];