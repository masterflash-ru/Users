<?php


namespace Mf\Users\Service\Admin\Zform\Plugin;

use Admin\Service\Zform\Plugin\AbstractPlugin;


class EditUserProfile extends AbstractPlugin
{

    protected $UserManager;
    
    public function __construct($UserManager)
    {
        $this->UserManager=$UserManager;
    }
    
    /**
    */
    public function iedit($postParameters,$getParameters)
    {
       // \Laminas\Debug\Debug::dump($postParameters);
        $this->UserManager->updateUserInfo ($postParameters["id"], $postParameters);
        $this->UserManager->setGroupIds($postParameters["id"],$postParameters["gr"]);
    }



}