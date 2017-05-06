<?php

namespace Empregos\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Empregos\Entity\Empregos;

/**
 * This form is used to collect post data.
 */
class AutoForm extends Form
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
            'name' => 'title',
            'attributes' => [
                'id' => 'title'
            ],
            'options' => [
                'label' => _('Title'),
            ],
        ]);
        
        // Add "content" field
        $this->add([
            'type'  => 'textarea',
            'name' => 'content',
            'attributes' => [                
                'id' => 'content'
            ],
            'options' => [
                'label' => _('Content'),
            ],
        ]);
        
        // Add "tags" field
        $this->add([
            'type'  => 'text',
            'name' => 'contact',
            'attributes' => [                
                'id' => 'contact'
            ],
            'options' => [
                'label' => _('Contact'),
            ],
        ]);
        
        if ($this->scenario == 'admin') {
            
        // Add "status" field
        $this->add([
            'type'  => 'select',
            'name' => 'status',
            'attributes' => [                
                'id' => 'status'
            ],
            'options' => [
                'label' => 'Status',
                'value_options' => [
                    Empregos::STATUS_APROVAR => _('Pending Approval'),
                    Empregos::STATUS_PUBLISHED => _('Published'),
                    Empregos::STATUS_DRAFT => _('Draft'),
                    Empregos::STATUS_EXPIRED => _('Expired'),
                ]
            ],
        ]);
        
        }else
        {
                   // Add "status" field
        $this->add([
            'type'  => 'select',
            'name' => 'status',
            'attributes' => [                
                'id' => 'status'
            ],
            'options' => [
                'label' => 'Status',
                'value_options' => [
                    Empregos::STATUS_APROVAR => _('Pending Approval'),
                    Empregos::STATUS_DRAFT => _('Draft'),
                ]
            ],
        ]); 
            
            
        }
        
        // Add the submit button
        $this->add([
            'type'  => 'submit',
            'name' => 'submit',
            'attributes' => [                
                'value' => _('Create'),
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
                'name'     => 'title',
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
        
        $inputFilter->add([
                'name'     => 'content',
                'required' => true,
                'filters'  => [                    
                //    ['name' => 'StripTags'],
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
                'name'     => 'contact',
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


