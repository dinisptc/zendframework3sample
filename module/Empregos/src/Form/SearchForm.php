<?php

namespace Empregos\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Empregos\Entity\Empregos;

/**
 * This form is used to collect post data.
 */
class SearchForm extends Form
{
    
        /**
     * Scenario ('create' or 'update').
     * @var string 
     */
    private $scenario;
    
    
    
    /**
     * Constructor.     
     */
    public function __construct($scenario = 'user')
    {
        // Define form name
        parent::__construct('post-form');
     
        // Set POST method for this form
        $this->setAttribute('method', 'post');
        
         // Save parameters for internal use.
        $this->scenario = $scenario;
                
        $this->addElements();
        $this->addInputFilter();  
        
    }
    
    /**
     * This method adds elements to form (input fields and submit button).
     */
    protected function addElements() 
    {
                
        // Add "title" field
        $this->add([        
            'type'  => 'text',
            'name' => 'search',
            'attributes' => [
                'id' => 'search'
            ],
            'options' => [
                'label' => _('Search'),
            ],
        ]);
        
       
        
        // Add the submit button
        $this->add([
            'type'  => 'submit',
            'name' => 'submit',
            'attributes' => [                
                'value' => _('Search'),
                'id' => 'submitbutton',
            ],
        ]);
    }
    
    /**
     * This method creates input filter (used for form filtering/validation).
     */
    private function addInputFilter() 
    {
        
        $inputFilter = new InputFilter();        
        $this->setInputFilter($inputFilter);
        
        $inputFilter->add([
                'name'     => 'search',
                'required' => true,
                'filters'  => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags'],
                    ['name' => 'StripNewlines'],
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 1024
                        ],
                    ],
                ],
            ]);
        
       
    }
}


