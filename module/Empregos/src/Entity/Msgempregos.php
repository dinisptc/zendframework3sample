<?php

//CREATE TABLE `msgauto` (
//	`id` INT(11) NOT NULL AUTO_INCREMENT,
//	`telefone` VARCHAR(500) NULL DEFAULT NULL,
//	`email` TEXT NOT NULL,
//	`mensagem` TEXT NOT NULL,
//	`iddoanuncioauto` INT(11) NOT NULL,
//	PRIMARY KEY (`id`)
//)
//COLLATE='utf8_general_ci'
//ENGINE=InnoDB
//;

namespace Empregos\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * This class represents a message Empregos.
 * @ORM\Entity
 * @ORM\Table(name="msgempregos")
 */
class Msgempregos 
{
     /**
     * @ORM\Id
     * @ORM\Column(name="id")
     * @ORM\GeneratedValue
     */
    protected $id;
    
    /** 
    * @ORM\Column(name="name")  
    */
    protected $name;
    
    
    /** 
    * @ORM\Column(name="email")  
    */
    protected $email;
    
    /** 
    * @ORM\Column(name="mensagem")  
    */
    protected $mensagem;  
    
    /** 
    * @ORM\Column(name="iddoanuncioauto")  
    */
    protected $iddoanuncioauto;
    
    
     /**
     * Constructor.
     */
    public function __construct() 
    {        
     
    }
    
    
     /**
     * Returns ID of this msg.
     * @return integer
     */
    public function getId() 
    {
        return $this->id;
    }

    /**
     * Sets ID of this tag.
     * @param int $id
     */
    public function setId($id) 
    {
        $this->id = $id;
    }
    
        
    /**
    * Returns name.
    * @return string
    */
    public function getName() 
    {
        return $this->name;
    }

    /**
     * Sets name.
     * @param string $name
     */
        public function setName($name) 
    {
        $this->name = $name;
    }
    
    
    /**
    * Returns email.
    * @return string
    */
    public function getEmail() 
    {
        return $this->email;
    }

    /**
     * Sets email.
     * @param string $email
     */
    public function setEmail($email) 
    {
        $this->email = $email;
    }
    
    
    /**
    * Returns mensagem.
    * @return string
    */
    public function getMensagem() 
    {
        return $this->mensagem;
    }

    /**
     * Sets mensagem.
     * @param string $mensagem
     */
    public function setMensagem($mensagem) 
    {
        $this->mensagem = $mensagem;
    }
    
    //`iddoanuncioauto` INT(11) NOT NULL,
     /**
     * Returns iddoanuncioauto.
     * @return integer
     */
    public function getIddoanuncioauto() 
    {
        return $this->iddoanuncioauto;
    }

    /**
     * Sets iddoanuncioauto.
     * @param integer $iddoanuncioauto
     */
    public function setIddoanuncioauto($iddoanuncioauto) 
    {
        $this->iddoanuncioauto = $iddoanuncioauto;
    }   
}
