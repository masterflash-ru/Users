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
        
            
            "read"=>[
                "EditUserPassword"=>[],
            ],
            "edit"=>[
                "EditUserPassword"=>[],
            ],
            
            /*поведение формы*/
            "actionsEvent"=>[
                /*что делать после успешной записи формы*/
               // "FormAfterSubmitOkEvent"=>'setTimeout(function(){$("#interfacesDialog").dialog("close");},500);',
            ],

            /*внешний вид*/
            "layout"=>[
                "caption" => "Изменить пароль",
                "rowModel" => [
                    'elements' => [
                        RowModelHelper::text("password",[
                            'options'=>[
                                "label"=>"Новый пароль"
                            ],
                        ]),
                        RowModelHelper::submit("submit",[
                            'attributes'=>['value' => 'Записать'],
                        ]),

                        //это ID юзера
                        RowModelHelper::hidden("id"),
                    ],
                    
                    /*конфигурация фильтров и валидаторов, СМ. Документацию к ZF3*/
                    'input_filter' => [
                        "password" => [
                            'required' => true,
                        ],
                    ],

                ],
            ],
        ],
];