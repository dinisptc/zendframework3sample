<?php
namespace User\Form;

use Zend\Form\Form;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;

/**
 * This form is used when changing user's password (to collect user's old password 
 * and new password) or when resetting user's password (when user forgot his password).
 */
class PesquisaUserForm extends Form
{   
    // There can be two scenarios - 'change' or 'reset'.
    private $scenario;
    
    /**
     * Constructor.
     * @param string $scenario Either 'change' or 'reset'.     
     */
    public function __construct()
    {
        // Define form name
        parent::__construct();
     
        //$this->scenario = $scenario;
        
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

                // Add "full_name" field
        $this->add([            
            'type'  => 'text',
            'name' => 'id',            
            'options' => [
                'label' => _('User ID'),
            ],
        ]);
        
        
        // Add the Submit button
        $this->add([
            'type'  => 'submit',
            'name' => 'submit',
            'attributes' => [                
                'value' => _('Search')
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
            // Add input for "full_name" field
        $inputFilter->add([
                'name'     => 'id',
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
       
    }
}

