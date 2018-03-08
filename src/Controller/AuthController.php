<?php

namespace Mf\Users\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\Result;
use Zend\Uri\Uri;
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
        $redirectUrl = (string)$this->params()->fromQuery('redirectUrl', '');
        if (strlen($redirectUrl)>2048) {
            throw new \Exception("Too long redirectUrl argument passed");
        }
        
        
        //форма авторизации
        $form = new LoginForm(); 
        $form->get('redirect_url')->setValue($redirectUrl);
        
        // Store login status.
        $isLoginError = false;
        
        //был POST?
        if ($this->getRequest()->isPost()) {
            
            //все данные из POST
            $data = $this->params()->fromPost();            
            
            $form->setData($data);
            
            //данные валидные?
            if($form->isValid()) {
                $data = $form->getData();

                $result = $this->authManager->login($data['email'], $data['password'], $data['remember_me']);
                
                //авторизовался нормально?
                if ($result->getCode() == Result::SUCCESS) {
                    
                    // Get redirect URL.
                    $redirectUrl = $this->params()->fromPost('redirect_url', '');
                    
                    if (!empty($redirectUrl)) {
                        $uri = new Uri($redirectUrl);
                        if (!$uri->isValid() || $uri->getHost()!=null)
                            throw new \Exception('Incorrect redirect URL: ' . $redirectUrl);
                    }

                    // If redirect URL is provided, redirect the user to that URL;
                    // otherwise redirect to Home page.
                    if(empty($redirectUrl)) {
                        return $this->redirect()->toRoute('home');
                    } else {
                        $this->redirect()->toUrl($redirectUrl);
                    }
                } else {
                    $isLoginError = true;
                }                
            } else {
                $isLoginError = true;
            }           
        } 
        
        return new ViewModel([
            'form' => $form,
            'isLoginError' => $isLoginError,
            'redirectUrl' => $redirectUrl
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
