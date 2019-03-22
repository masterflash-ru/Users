<?php
namespace Mf\Users\Service\Admin\JqGrid\Plugin;

use Admin\Service\JqGrid\Plugin\AbstractPlugin;


class ReadGroup extends AbstractPlugin
{

    protected $connection;
    
    public function __construct($connection)
    {
        $this->connection=$connection;
    }


    /**
    * запись изменений
    */
    public function ajaxRead()
    {
        $rs=$this->connection->Execute("select id,name from users_group order by name");
        $rez=[0=>""];
        while (!$rs->EOF){
            $rez[$rs->Fields->Item['id']->Value]=$rs->Fields->Item['name']->Value;
            $rs->moveNext();
        }
        return $rez;
    }
    /**
    * преобразование элементов colModel, например, для генерации списков
    * $colModel - элемент $colModel из конфигурации
    * возвращает тот же $colModel, с внесенными изменениями
    */
    public function colModel(array $colModel)
    {
        $rez=[0=>""];
        $rs=$this->connection->Execute("select id,name from users_group order by name");
        while (!$rs->EOF){
            $rez[$rs->Fields->Item["id"]->Value]=$rs->Fields->Item["name"]->Value;
            $rs->MoveNext();
        }
        
        $colModel["editoptions"]["value"]=$rez;
        
        return $colModel;
    }

}
