<?php
namespace Empregos\Form;

use Zend\Form\Form;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;


class MsgForm extends Form
{
    
     public function __construct()
    {
        // Define form name
        parent::__construct('msg');
     
        // Set POST method for this form
        $this->setAttribute('method', 'post');
        
     

        
        $this->addElements();
        $this->addInputFilter();          
    }   
    
    protected function addElements() 
    {
     

        

        $this->add([
            'name' => 'name',
            'attributes' => [
                'type'  => 'text',            
                'class'=>'textbox',
                //'placeholder' => _('Your Name'),
            ],
            'options' => [
                'label' => _('Your Name'),
            ],
        ]);
        
        $this->add([
            'name' => 'mensagem',
            'attributes' => [
                'required' => 'required',
                'type'  => 'textarea',
         
                
            ],
            'options' => [
                'label' => _('Your Message'),
            ],
        ]);
        
        $this->add([
            'name' => 'email',
            'attributes' => [
                'required' => 'required',
                'type'  => 'email',            
                'class'=>'textbox',
              
                
            ],
            'options' => [               
               'label' => _('Your Email'),
            ],
        ]);
        
        
        // Add the Submit button
        $this->add([
            'type'  => 'submit',
            'name' => 'submit',
          
            'attributes' => [                
                'value' => _('Send')
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
                'name'     => 'name',
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
                                
                ],
            ]);
        
                        // Add input for "full_name" field
        $inputFilter->add([
                'name'     => 'mensagem',
                'required' => true,
                'filters'  => [                    
                    ['name' => 'StringTrim'],
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 1,
                            'max' => 4000
                        ],
                    ],
                ],
            ]);
    }
}