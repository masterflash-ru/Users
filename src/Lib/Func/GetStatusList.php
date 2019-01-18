<?php
namespace Mf\Users\Lib\Func;


class GetStatusList
{


function __invoke($obj,$infa,$struct_arr,$pole_type,$pole_dop,$tab_name,$idname,$number,$id,$action)
{
$status=$obj->config["users"]["users_status"];
ksort($status);

foreach ($status as $k=>$status){
    $obj->dop_sql['name'][]=$status;
    $obj->dop_sql['id'][]=$k;

}
return $infa;

}



}