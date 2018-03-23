<?php
namespace Mf\Users\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Mf\Users\Form\UserForm;
use Mf\Users\Form\PasswordChangeForm;
use Mf\Users\Form\PasswordResetForm;
use ADO\Service\RecordSet;
use ADO\Service\Command;
use Mf\Permissions\Entity\Users;
use Zend\Mail;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Mime;
use Zend\Mime\Part as MimePart;

/**
 * This controller is responsible for user management (adding, editing, 
 * viewing users and changing user's password).
 */
class UserController extends AbstractActionController 
{
    /**
     */
    protected $connection;
    
    protected $translator;
    
    protected $config;
    
    /**
     * User manager.
     */
    protected $userManager;
    
    /**
     * Constructor. 
     */
    public function __construct($connection, $userManager,$config,$translator)
    {
        $this->connection = $connection;
        $this->userManager = $userManager;
        $this->config=$config;
        $translator->setLocale("ru");       //ставим ru, пока нет поддержки мультиязычности
        $this->translator=$translator;
    }
    
    /**
     * This is the default "index" action of the controller. It displays the 
     * list of users.
     */
    public function indexAction() 
    {
        $users = $this->entityManager->getRepository(User::class)
                ->findBy([], ['id'=>'ASC']);
        
        return new ViewModel([
            'users' => $users
        ]);
    } 
    
    
    /**
     * The "view" action displays a page allowing to view user's details.
     */
    public function viewAction() 
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        // Find a user with such ID.
        $user = $this->entityManager->getRepository(User::class)
                ->find($id);
        
        if ($user == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
                
        return new ViewModel([
            'user' => $user
        ]);
    }
    
    /**
     * The "edit" action displays a page allowing to edit user.
     */
    public function editAction() 
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        $user = $this->entityManager->getRepository(User::class)
                ->find($id);
        
        if ($user == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        // Create user form
        $form = new UserForm('update', $this->entityManager, $user);
        
        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {
            
            // Fill in the form with POST data
            $data = $this->params()->fromPost();            
            
            $form->setData($data);
            
            // Validate form
            if($form->isValid()) {
                
                // Get filtered and validated data
                $data = $form->getData();
                
                // Update the user.
                $this->userManager->updateUser($user, $data);
                
                // Redirect to "view" page
                return $this->redirect()->toRoute('users', 
                        ['action'=>'view', 'id'=>$user->getId()]);                
            }               
        } else {
            $form->setData(array(
                    'full_name'=>$user->getFullName(),
                    'email'=>$user->getEmail(),
                    'status'=>$user->getStatus(),                    
                ));
        }
        
        return new ViewModel(array(
            'user' => $user,
            'form' => $form
        ));
    }
    
    /**
     * This action displays a page allowing to change user's password.
     */
    public function changePasswordAction() 
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        $user = $this->entityManager->getRepository(User::class)
                ->find($id);
        
        if ($user == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        // Create "change password" form
        $form = new PasswordChangeForm('change');
        
        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {
            
            // Fill in the form with POST data
            $data = $this->params()->fromPost();            
            
            $form->setData($data);
            
            // Validate form
            if($form->isValid()) {
                
                // Get filtered and validated data
                $data = $form->getData();
                
                // Try to change password.
                if (!$this->userManager->changePassword($user, $data)) {
                    $this->flashMessenger()->addErrorMessage(
                            'Sorry, the old password is incorrect. Could not set the new password.');
                } else {
                    $this->flashMessenger()->addSuccessMessage(
                            'Changed the password successfully.');
                }
                
                // Redirect to "view" page
                return $this->redirect()->toRoute('users', 
                        ['action'=>'view', 'id'=>$user->getId()]);                
            }               
        } 
        
        return new ViewModel([
            'user' => $user,
            'form' => $form
        ]);
    }
    
    /**
     * Сброс пароля, путем генерации временного и записи его в спец поле таблицы, 
     * юзеру отправляется письмо из "стат-страниц" 
     */
    public function resetPasswordAction()
    {
        //проверим авторизован ли юзер?
        if ($this->user()->identity()){
            //да, переходим на страницу после авторизации, из конфига
            $this->redirect()->toRoute($this->config["users"]["routeNameAfterLogin"]);
        }
        $Error=null;
        //здесь создаем капчу исходя из настроек 
        $options=$this->config["captcha"]["options"][$this->config["captcha"]["adapter"]];
        $adapter= "\\".$this->config["captcha"]["adapter"];
        $captcha=new $adapter($options);
        $prg = $this->prg();
        if ($prg instanceof Response) {
            //сюда попадаем когда форма отправлена, производим редирект
            return $prg;
        }

        $view=new ViewModel();

        $form = new PasswordResetForm($captcha,$this->translator);
        
        if ($prg === false){
          //вывод страницы и формы
          $view->setVariables(["form"=>$form,"Error"=>$Error]);
          return $view;
        }
        $form->setData($prg);

        if($form->isValid()) {
            $data = $form->getData();
            // получим ID юзера по его логину, если он есть
            $c=new Command();
            $c->NamedParameters=true;
            $c->ActiveConnection=$this->connection;
            $p=$c->CreateParameter('login', adChar, adParamInput, 50, $data['login']);//генерируем объек параметров
            $c->Parameters->Append($p);//добавим в коллекцию
            $c->CommandText="select id from users where login=:login";
            
            $rs=new RecordSet();
            $rs->CursorType =adOpenKeyset;
            $rs->Open($c);
            
            $users= $rs->FetchEntity(Users::class);
            if ($users!=null) {
                /*новый пароль и дата его годности*/
                $data["temp_password"]=$this->userManager->generatePasswordReset();
                $data["temp_date"] = date('Y-m-d H:i:s',time()+86400);
                
                /*обновить в профиле*/
                $this->userManager->updateUserInfo ($users->getId(), $data);
                
                /*текст письма юзеру*/
                $mess=$this->Statpage("RESET_PASSWORD",["pageType"=>3]);
                
                /*адрес сервера-сайта*/
                $httpHost = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'localhost';
                $mess=str_replace("{SERVER}",$httpHost,$mess);
                $mess=str_replace("{PASSWORD}",$data["temp_password"],$mess);

                $text           = new MimePart($mess);
                $text->type     = Mime::TYPE_HTML;
                $text->charset  = 'utf-8';
                $text->encoding = Mime::ENCODING_QUOTEDPRINTABLE;
                $body = new MimeMessage();
                $body->setParts([ $text]);
                
                $mail = new Mail\Message();
                $mail->setEncoding("UTF-8");
                $mail->setBody($body);
                $mail->setFrom($this->config["email_robot"]);
                $mail->addTo($data['login']);
                $mail->setSubject('Password Reset');
                $transport = new Mail\Transport\Sendmail();
                $transport->send($mail);
                $view->setTemplate("mf/users/user/reset-password-ok");

            } else {
                $Error="Неверный E-mail";
            }
        }

        $view->setVariables(["form"=>$form,"Error"=>$Error]);
        return $view;
    }
    
    /**
     * This action displays an informational message page. 
     * For example "Your password has been resetted" and so on.
     * /
    public function messageAction() 
    {
        // Get message ID from route.
        $id = (string)$this->params()->fromRoute('id');
        
        // Validate input argument.
        if($id!='invalid-email' && $id!='sent' && $id!='set' && $id!='failed') {
            throw new \Exception('Invalid message ID specified');
        }
        
        return new ViewModel([
            'id' => $id
        ]);
    }
    
    /**
     * This action displays the "Reset Password" page. 
     */
    public function setPasswordAction()
    {
        $token = $this->params()->fromQuery('token', null);
        
        // Validate token length
        if ($token!=null && (!is_string($token) || strlen($token)!=32)) {
            throw new \Exception('Invalid token type or length');
        }
        
        if($token===null || 
           !$this->userManager->validatePasswordResetToken($token)) {
            return $this->redirect()->toRoute('users', 
                    ['action'=>'message', 'id'=>'failed']);
        }
                
        // Create form
        $form = new PasswordChangeForm('reset');
        
        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {
            
            // Fill in the form with POST data
            $data = $this->params()->fromPost();            
            
            $form->setData($data);
            
            // Validate form
            if($form->isValid()) {
                
                $data = $form->getData();
                                               
                // Set new password for the user.
                if ($this->userManager->setNewPasswordByToken($token, $data['new_password'])) {
                    
                    // Redirect to "message" page
                    return $this->redirect()->toRoute('users', 
                            ['action'=>'message', 'id'=>'set']);                 
                } else {
                    // Redirect to "message" page
                    return $this->redirect()->toRoute('users', 
                            ['action'=>'message', 'id'=>'failed']);                 
                }
            }               
        } 
        
        return new ViewModel([                    
            'form' => $form
        ]);
    }
}


