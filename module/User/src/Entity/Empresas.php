<?php

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


namespace User\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This class represents a registered company.
 * @ORM\Entity()
 * @ORM\Table(name="empresas")
 */
class Empresas 
{
    
     /**
     * @ORM\Id
     * @ORM\Column(name="id")
     */
    protected $id;
    
    
    
    /**
    * @ORM\Column(name="designacao")  
    */
    protected $designacao;
    
    
    /**
    * @ORM\Column(name="descricao")  
    */
    protected $descricao;
    
    /**
    * @ORM\Column(name="endereco")  
    */
    protected $endereco;
    
        
    /**
    * @ORM\Column(name="site")  
    */
    protected $site;
    
    /**
    * @ORM\Column(name="facebook")  
    */
    protected $facebook;
    
    
    /**
    * @ORM\Column(name="linkedin")  
    */
    protected $linkedin;
    
    /**
    * @ORM\Column(name="twitter")  
    */
    protected $twitter;
        
    /**
    * @ORM\Column(name="telefone")  
    */
    protected $telefone;
    
    
    /**
    * @ORM\Column(name="email")  
    */
    protected $email;
    
    
    /**
    * @ORM\Column(name="fax")  
    */
    protected $fax;
    
    /**
    * @ORM\Column(name="identidade")  
    */
    protected $identidade;
    
    
    /**
     * Returns user ID.
     * @return string
     */
    public function getId() 
    {
        return $this->id;
    }

    /**
     * Sets user ID. 
     * @param string $id    
     */
    public function setId($id) 
    {
        $this->id = $id;
    }
    
            /**
     * Returns designacao.
     * @return string
     */
    public function getDesignacao() 
    {
        return $this->designacao;
    }

    /**
     * Sets designacao. 
     * @param string $designacao    
     */
    public function setDesignacao($designacao) 
    {
        $this->designacao = $designacao;
    }
    
    
     /**
     * Returns descricao.
     * @return string 
     */
    public function getDescricao() 
    {
        return $this->descricao;
    }

    /**
     * Sets descricao. 
     * @param string $descricao    
     */
    public function setDescricao($descricao) 
    {
        $this->descricao = $descricao;
    }
    
    
    
     /**
     * Returns endereco.
     * @return string 
     */
    public function getEndereco() 
    {
        return $this->endereco;
    }

    /**
     * Sets endereco. 
     * @param string $endereco    
     */
    public function setEndereco($endereco) 
    {
        $this->endereco = $endereco;
    }
    
    
    /**
     * Returns site.
     * @return string 
     */
    public function getSite() 
    {
        return $this->site;
    }

    /**
     * Sets site. 
     * @param string $site   
     */
    public function setSite($site) 
    {
        $this->site = $site;
    }
   
            
    /**
     * Returns linkedin.
     * @return string 
     */
    public function getLinkedin() 
    {
        return $this->linkedin;
    }

    /**
     * Sets linkedin. 
     * @param string $linkedin   
     */
    public function setLinkedin($linkedin) 
    {
        $this->linkedin = $linkedin;
    }
    

    /**
     * Returns twitter.
     * @return string 
     */
    public function getTwitter() 
    {
        return $this->twitter;
    }

    /**
     * Sets twitter. 
     * @param string $twitter   
     */
    public function setTwitter($twitter) 
    {
        $this->twitter = $twitter;
    }
    
    
    
    
    /**
     * Returns telefone.
     * @return string 
     */
    public function getTelefone() 
    {
        return $this->telefone;
    }

    /**
     * Sets telefone. 
     * @param string $telefone   
     */
    public function setTelefone($telefone) 
    {
        $this->telefone = $telefone;
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
     * Returns fax.
     * @return string 
     */
    public function getFax() 
    {
        return $this->fax;
    }

    /**
     * Sets fax. 
     * @param string $fax   
     */
    public function setFax($fax) 
    {
        $this->fax = $fax;
    }
    
            
     /**
     * Returns identidade.
     * @return string 
     */
    public function getIdentidade() 
    {
        return $this->identidade;
    }

    /**
     * Sets identidade. 
     * @param string $identidade   
     */
    public function setIdentidade($identidade) 
    {
        $this->identidade = $identidade;
    }
    
    
}



