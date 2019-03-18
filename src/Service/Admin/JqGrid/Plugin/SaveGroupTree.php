<?php
namespace Mf\Users\Service\Admin\JqGrid\Plugin;

use ADO\Service\RecordSet;
use Admin\Service\JqGrid\Plugin\AbstractPlugin;


class SaveGroupTree extends AbstractPlugin
{

    protected $connection;
    
    public function __construct($connection)
    {
        $this->connection=$connection;
    }

    /**
    * запись изменений
    */
    public function add(array $postParameters)
    {
        $postParameters["id"]=0;
        return $this->edit($postParameters);
    }

    
    /**
    * запись изменений
    */
    public function edit(array $postParameters)
    {
/*array(5) {
  ["id"] => string(2) "11"
  ["description"] => string(33) "описание группы 111"
  ["name"] => string(7) "1111111"
  ["parent_group"] => string(3) "6,7"
  ["oper"] => string(4) "edit"
}*/
//print_r($postParameters);

        $this->connection->BeginTrans();
        $rs=new RecordSet();
        $rs->CursorType = adOpenKeyset;
        $rs->open("SELECT * FROM users_group",$this->connection);
        if (trim($postParameters["parent_group"])) {
            $parent_group=explode(",",$postParameters["parent_group"]);
        } else {$parent_group=[];}
        
        if (empty($postParameters['id'])){
            //	добавление
            $rs->AddNew();
            $rs->Fields->Item['name']->Value=$postParameters['name'];
            $rs->Fields->Item['description']->Value=$postParameters['description'];
            $rs->Update();
            $id=(int)$rs->Fields->Item['id']->Value;
        } else {
             //удалим старые связи
            $this->connection->Execute("delete from users_group_tree where id=".(int)$postParameters['id']);
            $rs->Find("id=".(int)$postParameters['id'],0,adSearchForward);
            $rs->Fields->Item['name']->Value=$postParameters['name'];
            $rs->Fields->Item['description']->Value=$postParameters['description'];
            $rs->Update();
            $id=(int)$postParameters['id'];
        }
        
        if (!empty($parent_group)){
            $rs1=new RecordSet();
            $rs1->CursorType = adOpenKeyset;
            $rs1->open("SELECT * FROM users_group_tree where id={$id}",$this->connection);
            foreach ($parent_group as $parent_id){
                if ($postParameters['id']==$parent_id){continue;}
                $rs1->AddNew();
                $rs1->Fields->Item['id']->Value=$id;
                $rs1->Fields->Item['parent_id']->Value=$parent_id;
                $rs1->Update();
            }
        }
        $this->connection->CommitTrans();
}
    /**
    * удаление
    */
    public function del(array $postParameters)
    {
        $id=(int)$postParameters["id"];
        $this->connection->BeginTrans();
        $this->connection->Execute("delete from users_group_tree where id=$id");
        $this->connection->Execute("delete from users_group where id=$id");
        $this->connection->CommitTrans();
        
    }
}
