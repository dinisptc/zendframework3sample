<?php

//CREATE TABLE `empresas` (
//	`id` VARCHAR(255) NOT NULL,
//	`designacao` VARCHAR(500) NOT NULL,
//	`descricao` TEXT NULL,
//	`endereco` TEXT NULL,
//	`site` VARCHAR(1000) NOT NULL,
//	`facebook` VARCHAR(500) NULL DEFAULT NULL,
//	`linkedin` VARCHAR(500) NULL DEFAULT NULL,
//	`twitter` VARCHAR(500) NULL DEFAULT NULL,
//	`telefone` VARCHAR(500) NULL DEFAULT NULL,
//	`email` VARCHAR(500) NOT NULL,
//	`fax` VARCHAR(500) NULL DEFAULT NULL,
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
}

