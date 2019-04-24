<?php
namespace Mf\Users\Service\Admin\JqGrid\Plugin;

use Admin\Service\JqGrid\Plugin\AbstractPlugin;


class SaveUser extends AbstractPlugin
{

    protected $connection;
    protected $UserManager;
    
    public function __construct($connection,$UserManager)
    {
        $this->connection=$connection;
        $this->UserManager=$UserManager;
    }

public function edit(array $postParameters)
{
/*array(6) {
  ["login"] => string(5) "admin"
  ["name"] => string(2) "11"
  ["full_name"] => string(2) "22"
  ["user_groups"] => string(4) "1,13"
  ["oper"] => string(4) "edit"
  ["id"] => string(2) "11"
}*/
    $this->UserManager->updateUserInfo ($postParameters["id"], $postParameters);
    $user_groups=explode(",",$postParameters["user_groups"]);
    $this->UserManager->setGroupIds($postParameters["id"],$user_groups);
}

public function add(array $postParameters)
{
/*array(6) {
  ["login"] => string(15) "342523452345345"
  ["name"] => string(7) "2352345"
  ["full_name"] => string(10) "wrtewrtwet"
  ["user_groups"] => string(5) "1,2,6"
  ["oper"] => string(3) "add"
  ["id"] => string(6) "_empty"
}*/
    $user=$this->UserManager->addUser ( $postParameters);
    $user_groups=explode(",",$postParameters["user_groups"]);
    $this->UserManager->setGroupIds($user->getId(),$user_groups);
}
    
public function del(array $postParameters)
{
/*array(6) {
  ["oper"] => string(3) "del"
  ["id"] => string(6) "123"
}*/
    $this->UserManager->updateUserInfo ($postParameters["id"], ["status"=>-1]);
}

}
