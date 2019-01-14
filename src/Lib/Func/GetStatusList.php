<?php
namespace Mf\Users\Lib\Func;


class GetStatusList
{


function __invoke($obj,$infa,$struct_arr,$pole_type,$pole_dop,$tab_name,$idname,$const,$id,$action)

{


foreach ($obj->config["users"]["users_status"] as $k=>$status){
    $obj->dop_sql['name'][]=$status;
    $obj->dop_sql['id'][]=$k;

}

return $infa;

}



}