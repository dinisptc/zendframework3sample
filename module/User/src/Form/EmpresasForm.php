<?php
namespace User\Form;

use Zend\Form\Form;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;
use User\Validator\EmpresaExistsValidator;

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
        
        // Add "descricao" field
        $this->add([            
            'type'  => 'textarea',
            'name' => 'descricao',            
            'options' => [
                'label' => _('Company Description'),
            ],
        ]);
        
        // Add "endereco" field
        $this->add([            
            'type'  => 'textarea',
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
                        'name' => EmpresaExistsValidator::class,
                        'options' => [
                            'entityManager' => $this->entityManager,
                            'empresa' => $this->empresas
                        ],
                    ],                    
                ],
            ]);     
        
        
        
        //designacao
        // Add input for "designacao" field
        $inputFilter->add([
                'name'     => 'designacao',
                'required' => true,
                'filters'  => [                    
                    ['name' => 'StringTrim'],
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 500
                        ],
                    ],
                ],
            ]);
        
        

        $inputFilter->add([
                'name'     => 'endereco',
                'required' => false,
                'filters'  => [                    
       
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 1000000
                        ],
                    ],
                ],
            ]);
        
        $inputFilter->add([
                'name'     => 'descricao',
                'required' => false,
                'filters'  => [                    
          
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 1000000
                        ],
                    ],
                ],
            ]); 
        
        //site
        // Add input for "site" field
        $inputFilter->add([
                'name'     => 'site',
                'required' => true,
                'filters'  => [                    
                    ['name' => 'StringTrim'],
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 1000
                        ],
                    ],
                ],
            ]);
        
        
        
        //facebook
        // Add input for "facebook" field
        $inputFilter->add([
                'name'     => 'facebook',
                'required' => false,
                'filters'  => [                    
                    ['name' => 'StringTrim'],
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 1000
                        ],
                    ],
                ],
            ]);
   
        
                //linkedin
        // Add input for "linkedin" field
        $inputFilter->add([
                'name'     => 'linkedin',
                'required' => false,
                'filters'  => [                    
                    ['name' => 'StringTrim'],
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 1000
                        ],
                    ],
                ],
            ]);
        
         
                //twitter
        // Add input for "twitter" field
        $inputFilter->add([
                'name'     => 'twitter',
                'required' => false,
                'filters'  => [                    
                    ['name' => 'StringTrim'],
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 1000
                        ],
                    ],
                ],
            ]);
        
        //telefone
        // Add input for "telefone" field
        $inputFilter->add([
                'name'     => 'telefone',
                'required' => false,
                'filters'  => [                    
                    ['name' => 'StringTrim'],
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 500
                        ],
                    ],
                ],
            ]);
        
        
        

        

        
        
                //fax
        // Add input for "fax" field
        $inputFilter->add([
                'name'     => 'fax',
                'required' => false,
                'filters'  => [                    
                    ['name' => 'StringTrim'],
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 500
                        ],
                    ],
                ],
            ]);
        
        

    }  
    
}