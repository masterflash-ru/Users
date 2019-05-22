<?php
namespace Mf\Users\Service\Admin\JqGrid\Plugin;

use Admin\Service\JqGrid\Plugin\AbstractPlugin;


class UserStatus extends AbstractPlugin
{

    protected $config;

    
    public function __construct($config)
    {
        $this->config=$config;

    }

    /**
    * преобразование элементов colModel, например, для генерации списков
    * $colModel - элемент $colModel из конфигурации
    * возвращает тот же $colModel, с внесенными изменениями
    */
    public function colModel(array $colModel, array $toolbarData=[])
    {
        unset($this->config[-1]);
        $colModel["editoptions"]["value"]=$this->config;
        
        return $colModel;
    }

}
