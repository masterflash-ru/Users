<?php

namespace Mf\Users\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\Result;
//use Zend\Uri\Uri;
use Mf\Users\Form\LoginForm;

/**
 * This controller is responsible for letting the user to log in and log out.
 */
class AuthController extends AbstractActionController
{
    
    /**
    * менеджер авторизации
     */
    protected $authManager;
    

    public function __construct($authManager)
    {
        $this->authManager = $authManager;
    }
    
    /**
     * вывод формы авторизации и обработка информации из нее (POST)
     */
    public function loginAction()
    {
        $prg = $this->prg();
        if ($prg instanceof Response) {
            //сюда попадаем когда форма отправлена, производим редирект
            return $prg;
        }

        $view=new ViewModel();
        $isLoginError=false;
        //форма авторизации
        $form = new LoginForm(); 
        
        if ($prg === false){
          //вывод страницы и формы
          $view->setVariables(["form"=>$form]);
          return $view;
        }

        

        $form->setData($prg);        
        //данные валидные?
        if($form->isValid()) {
            $data = $form->getData();

            $result = $this->authManager->login($data['login'], $data['password'], $data['remember_me']);
                
            //авторизовался нормально?
            if ($result->getCode() == Result::SUCCESS) {
                    
                    $redirectUrl = $this->params()->fromPost('redirect_url', '');
                    
                } else {
                    $isLoginError = true;
                }                
            } else {
                $isLoginError = true;
            }           

        
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
