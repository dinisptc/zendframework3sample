<?php
namespace Application\Form;

use Zend\Form\Form;


class PaypaloneForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('paypalone');

        $this->setAttribute('method', 'post');
        
        


        $this->add(array(
                'name' => 'submit',
                'attributes' => array(
                    'type'  => 'image',
                    'src'   => '/img/paypal.jpg',
                    //'height'=> '28',
                    //'width' => '98',
                    //'border'=> '0',
                    'alt'   => 'Paypal'
                ),
            ));

        
        
//        $this->add(array(
//            'name' => 'submit',
//            'attributes' => array(
//                'type'  => 'submit',            
//                'value' => _('Pay with Paypal'),
//                'id' => 'submitbutton',                 
//                'class' => 'btn btn-success',      
//            ),
//        ));

    }
}