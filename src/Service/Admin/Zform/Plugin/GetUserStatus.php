<?php


namespace Mf\Users\Service\Admin\Zform\Plugin;

use Admin\Service\Zform\Plugin\AbstractPlugin;


class GetUserStatus extends AbstractPlugin
{

    protected $config;
    
    public function __construct($config)
    {
        $this->config=$config;
    }
    
    /**
    * преобразование элементов rowModel, например, для генерации списков
    * $rowModel - элемент $rowModel из конфигурации
    * возвращает тот же $rowModel, с внесенными изменениями
    */
    public function rowModel(array $rowModel)
    {
        $config=$this->config['users_status'];
        $rez=[];
        if (is_array($config)){
            foreach ($config as $s=>$v){
                $rez[$s]=$v;
            }
        }
        $rowModel['options']["value_options"]=$rez;
        
        return $rowModel;
    }



}