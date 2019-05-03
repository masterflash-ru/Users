<?php


namespace Mf\Users\Service\Admin\Zform\Plugin;

use Admin\Service\Zform\Plugin\AbstractPlugin;


class EditUserPassword extends AbstractPlugin
{

    protected $UserManager;
    
    public function __construct($UserManager)
    {
        $this->UserManager=$UserManager;
    }
    
    /**
    * запись
    */
    public function edit($postParameters,$getParameters)
    {
       // \Zend\Debug\Debug::dump($postParameters);
        $this->UserManager->updateUserInfo ($postParameters["id"], $postParameters);
    }

    /**
    *чтение
    */
    public function read($getParameters)
    {
        return $getParameters;
    }


}