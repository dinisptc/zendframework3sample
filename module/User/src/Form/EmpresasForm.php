<?php
namespace Empresas\Form;

use Zend\Form\Form;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;
//use User\Validator\UserExistsValidator;

/**
 * This form is used to collect user's email, full name, password and status. The form 
 * can work in two scenarios - 'create' and 'update'. In 'create' scenario, user
 * enters password, in 'update' scenario he/she doesn't enter password.
 */
class EmpresasForm extends Form
{
    /**
     * Scenario ('create' or 'update').
     * @var string 
     */
    private $scenario;
    
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager 
     */
    private $entityManager = null;
    
    /**
     * Current user.
     * @var Empresas\Entity\Empresas 
     */
    private $empresas = null;
    
    /**
     * Constructor.     
     */
    public function __construct($scenario = 'create', $entityManager = null, $empresas = null)
    {
        // Define form name
        parent::__construct('empresas-form');
     
        // Set POST method for this form
        $this->setAttribute('method', 'post');
        
        // Save parameters for internal use.
        $this->scenario = $scenario;
        $this->entityManager = $entityManager;
        $this->empresas = $empresas;
        
        $this->addElements();
        $this->addInputFilter();          
    }
    
    
     /**
     * This method adds elements to form (input fields and submit button).
     */
    protected function addElements() 
    {
        
        //CREATE TABLE `empresas` (
        //	`id` VARCHAR(255) NOT NULL,
        //	`designacao` VARCHAR(500) NOT NULL,
        //	`descricao` TEXT NULL,
        //	`endereco` TEXT NULL,
        //	`site` VARCHAR(1000) NOT NULL,
        //	`facebook` VARCHAR(1000) NULL DEFAULT NULL,
        //	`linkedin` VARCHAR(1000) NULL DEFAULT NULL,
        //	`twitter` VARCHAR(1000) NULL DEFAULT NULL,
        //	`telefone` VARCHAR(500) NULL DEFAULT NULL,
        //	`email` VARCHAR(500) NOT NULL,
        //	`fax` VARCHAR(500) NULL DEFAULT NULL,
        //	`identidade` VARCHAR(255) NOT NULL,
        //	PRIMARY KEY (`id`)
        //)
        //COLLATE='utf8_general_ci'
        //ENGINE=InnoDB
        //;

        
        
        
        // Add "email" field
        $this->add([            
            'type'  => 'text',
            'name' => 'email',
            'options' => [
                'label' => _('E-mail'),
            ],
        ]);
        
        
        // Add "designacao" field
        $this->add([            
            'type'  => 'text',
            'name' => 'designacao',            
            'options' => [
                'label' => _('Company Name'),
            ],
        ]);
        
        // Add "endereco" field
        $this->add([            
            'type'  => 'text',
            'name' => 'endereco',            
            'options' => [
                'label' => _('Company Address'),
            ],
        ]);
        
        // Add "site" field
        $this->add([            
            'type'  => 'text',
            'name' => 'site',            
            'options' => [
                'label' => _('Company URL'),
            ],
        ]);
        
        // Add "facebook" field
        $this->add([            
            'type'  => 'text',
            'name' => 'facebook',            
            'options' => [
                'label' => _('Company Facebook'),
            ],
        ]);
        
        
        // Add "linkedin" field
        $this->add([            
            'type'  => 'text',
            'name' => 'linkedin',            
            'options' => [
                'label' => _('Company Linkedin'),
            ],
        ]);
        
        // Add "twitter" field
        $this->add([            
            'type'  => 'text',
            'name' => 'twitter',            
            'options' => [
                'label' => _('Company Twitter'),
            ],
        ]);
        
        // Add "telefone" field
        $this->add([            
            'type'  => 'text',
            'name' => 'telefone',            
            'options' => [
                'label' => _('Company Telefone'),
            ],
        ]);
        
        //email
        // Add "fax" field
        $this->add([            
            'type'  => 'text',
            'name' => 'fax',            
            'options' => [
                'label' => _('Company Fax'),
            ],
        ]);
        
        
        
        // Add the Submit button
        $this->add([
            'type'  => 'submit',
            'name' => 'submit',          
            'attributes' => [                
                'value' => 'Create'
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
                'name'     => 'email',
                'required' => true,
                'filters'  => [
                    ['name' => 'StringTrim'],                    
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 128
                        ],
                    ],
                    [
                        'name' => 'EmailAddress',
                        'options' => [
                            'allow' => \Zend\Validator\Hostname::ALLOW_DNS,
                            'useMxCheck'    => false,                            
                        ],
                    ],
                    [
                        'name' => UserExistsValidator::class,
                        'options' => [
                            'entityManager' => $this->entityManager,
                            'user' => $this->user
                        ],
                    ],                    
                ],
            ]);     
        
        // Add input for "full_name" field
        $inputFilter->add([
                'name'     => 'full_name',
                'required' => true,
                'filters'  => [                    
                    ['name' => 'StringTrim'],
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 512
                        ],
                    ],
                ],
            ]);
        
        if ($this->scenario == 'create') {
            
            // Add input for "password" field
            $inputFilter->add([
                    'name'     => 'password',
                    'required' => true,
                    'filters'  => [                        
                    ],                
                    'validators' => [
                        [
                            'name'    => 'StringLength',
                            'options' => [
                                'min' => 6,
                                'max' => 64
                            ],
                        ],
                    ],
                ]);
            
            // Add input for "confirm_password" field
            $inputFilter->add([
                    'name'     => 'confirm_password',
                    'required' => true,
                    'filters'  => [                        
                    ],                
                    'validators' => [
                        [
                            'name'    => 'Identical',
                            'options' => [
                                'token' => 'password',                            
                            ],
                        ],
                    ],
                ]);
        }
        
        // Add input for "status" field
        $inputFilter->add([
                'name'     => 'status',
                'required' => true,
                'filters'  => [                    
                    ['name' => 'ToInt'],
                ],                
                'validators' => [
                    ['name'=>'InArray', 'options'=>['haystack'=>[1, 2]]]
                ],
            ]);   
        
                // Add input for "status" field
        $inputFilter->add([
                'name'     => 'perfil',
                'required' => true,
                'filters'  => [                    
                    ['name' => 'ToInt'],
                ],                
                'validators' => [
                    ['name'=>'InArray', 'options'=>['haystack'=>[0, 1, 2]]]
                ],
            ]); 
    }  
    
}