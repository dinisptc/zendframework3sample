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
        //
        //
        // Add "email" field
        $this->add([            
            'type'  => 'text',
            'name' => 'email',
            'options' => [
                'label' => _('E-mail'),
            ],
        ]);
        
        
    }
    
    
    
}