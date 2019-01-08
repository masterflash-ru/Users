<?php
namespace Mf\Users\Lib\Func;

use ADO\Service\RecordSet;

class SaveUser
{


public function __invoke($obj,$tab_rec,$struct0,$struct2,$tab_name,$const,$row_item,$a,$b,$action)
{


    //запись строки по сути только добавление новых
    /*
    array(6) {
  ["login"] => string(12) "egfdghdfhfgh"
  ["name"] => string(0) ""
  ["full_name"] => string(0) ""
  ["date_registration"] => NULL
  ["status"] => string(1) "0"
  ["id"] => int(0)
}*/
    if ($action==-2) {
        $rs=new RecordSet();
        $rs->CursorType = adOpenKeyset;
        $rs->open("SELECT * FROM users where id=1",$obj->connection);

        $rs1=new RecordSet();
        $rs1->CursorType = adOpenKeyset;
        $rs1->open("SELECT * FROM users_ext where id=1",$obj->connection);
        
        $obj->connection->BeginTrans();
        $rs->AddNew();
        $rs->Fields->Item['login']->Value=$tab_rec["login"];
        $rs->Fields->Item['name']->Value=$tab_rec["name"];
        $rs->Fields->Item['full_name']->Value=$tab_rec["full_name"];
        $rs->Fields->Item['date_registration']->Value=$tab_rec["date_registration"];
        $rs->Fields->Item['status']->Value=$tab_rec["status"];
        $rs->Update();
        
        
        
        
        
        $rs1->AddNew();
        $rs1->Fields->Item['id']->Value=$rs->Fields->Item['id']->Value;
        $rs1->Update();
        
        $obj->connection->CommitTrans();

	}
return true;
}

}
