<?php


namespace Mf\Users\Service\Admin\Zform\Plugin;

use Admin\Service\Zform\Plugin\AbstractPlugin;
use Zend\Form\FormInterface;

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
    * $form - экземпляр формы, в нее нужно заносить информацию
    */
    public function rowModel(array $rowModel, FormInterface $form)
    {
        $config=$this->config['users_status'];
        unset($config[-1]);
        $rez=[];
        if (is_array($config)){
            foreach ($config as $s=>$v){
                $rez[$s]=$v;
            }
        }
        $form->get($rowModel["name"])->setValueOptions($rez);
        

    }



}