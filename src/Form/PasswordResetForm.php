<?php
namespace Mf\Users\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\Validator\AbstractValidator;
/**
 * This form is used to collect user's E-mail address (used to recover password).
 */
class PasswordResetForm extends Form
{
    protected $captcha;
    /**
     * Constructor.     
     */
    public function __construct($captcha,$translator=null)
    {
        if ($translator){
            AbstractValidator::setDefaultTranslator($translator);
        }

    
        $this->captcha=$captcha;
        parent::__construct('password-reset-form');
     
        // Set POST method for this form
        $this->setAttribute('method', 'post');
                
        $this->addElements();
        $this->addInputFilter();          
    }
    
    /**
     * This method adds elements to form (input fields and submit button).
     */
    protected function addElements() 
    {
        // Add "email" field
        $this->add([            
            'type'  => 'email',
            'name' => 'login',
            'options' => [
                'label' => 'Ваш E-mail',
            ],
        ]);
        
		 $this->add([
					'type' =>"Captcha",
					'name' => 'captcha',
					'options' => [
						'label' => '',
						'captcha' =>$this->captcha,
					],
					'attributes' => [                
						'placeholder'=>'Символы с картинки',
					],
				]);
        
        // Add the CSRF field
        $this->add([
            'type' => 'csrf',
            'name' => 'csrf',
            'options' => [
                'csrf_options' => [
                'timeout' => 600
                ]
            ],
        ]);
        
        // Add the Submit button
        $this->add([
            'type'  => 'submit',
            'name' => 'submit',
            'attributes' => [                
                'value' => 'Получить пароль',
                'id' => 'submit',
            ],
        ]);       
    }
    
    /**
     * This method creates input filter (used for form filtering/validation).
     */
    private function addInputFilter() 
    {
        // Create main input filter
        $inputFilter = new InputFilter();        
        $this->setInputFilter($inputFilter);
                
        // Add input for "email" field
        $inputFilter->add([
                'name'     => 'login',
                'required' => true,
                'filters'  => [
                    ['name' => 'StringTrim'],                    
                ],                
                'validators' => [
                    [
                        'name' => 'EmailAddress',
                        'options' => [
                            'allow' => \Zend\Validator\Hostname::ALLOW_DNS,
                            'useMxCheck'    => false,                            
                        ],
                    ],
                ],
            ]);                     
    }        
}
