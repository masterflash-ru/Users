<?php

namespace Mf\Users\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\Result;
use Mf\Users\Form\LoginForm;

/**
 * контроллер выводящий форму авторизации и собственно сама авторизация при помощи сервиса
 */
class AuthController extends AbstractActionController
{
    
    /**
    * менеджер авторизации
     */
    protected $authManager;
    
    protected $translator;
    
    /**
    * config - модуля (секция users)
    */
    protected $config;
    

    public function __construct($authManager,$config,$translator)
    {
        $this->authManager = $authManager;
        $this->config=$config["users"];
        $translator->setLocale("ru");       //ставим ru, пока нет поддержки мультиязычности
        $this->translator=$translator;

    }
    
    /**
     * вывод формы авторизации и обработка информации из нее (POST)
     */
    public function loginAction()
    {
        //проверим авторизован ли юзер?
        if ($this->user()->identity()){
            //да, переходим на страницу после авторизации, из конфига
            $this->redirect()->toRoute($this->config["routeNameAfterLogin"]);
        }
        
        $prg = $this->prg();
        if ($prg instanceof Response) {
            //сюда попадаем когда форма отправлена, производим редирект
            return $prg;
        }

        $view=new ViewModel();
        $isLoginError=false;
        //форма авторизации
        $form = new LoginForm($this->translator); 
        
        if ($prg === false){
          //вывод страницы и формы
          $view->setVariables(["form"=>$form,'isLoginError' => $isLoginError]);
          return $view;
        }

        $form->setData($prg);        
        //данные валидные?
        if($form->isValid()) {
            $data = $form->getData();
            $result = $this->authManager->login($data['login'], $data['password'], $data['remember_me']);

            //авторизовался нормально?
            if ($result->getCode() == Result::SUCCESS) {
                $this->redirect()->toRoute($this->config["routeNameAfterLogin"]);
            } else {$isLoginError = true;}                
        } else {$isLoginError = true;}           
    
        return new ViewModel([
            'form' => $form,
            'isLoginError' => $isLoginError,
        ]);
    }
    
    /**
     * The "logout" action performs logout operation.
     */
    public function logoutAction() 
    {        
        $this->authManager->logout();
        
        return $this->redirect()->toRoute('login');
    }
}
