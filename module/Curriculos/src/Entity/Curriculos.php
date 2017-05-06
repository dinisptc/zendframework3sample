<?php
namespace Curriculos\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;





/**
 * This class represents a single post in a blog.
 * @ORM\Entity(repositoryClass="\Empregos\Repository\EmpregosRepository")
 * @ORM\Table(name="empregos")
 */
class Curriculos 
{
    
    // Post status constants.
    const STATUS_APROVAR     = 0; // Aprovar.
    const STATUS_DRAFT       = 1; // Draft.
    const STATUS_PUBLISHED   = 2; // Published.
    const STATUS_EXPIRED   = 3; // Expirou.
    
    
     /**
     * @ORM\Id
     * @ORM\Column(name="id")
     */
    protected $id;
    
    
    /** 
    * @ORM\Column(name="title")  
    */
    protected $title;
    
    /** 
    * @ORM\Column(name="content")  
    */
    protected $content;
    
    /** 
    * @ORM\Column(name="status")  
    */
    protected $status;
    
    
    /**
    * @ORM\Column(name="date_created")  
    */
    protected $dateCreated;
    
    
    /** 
    * @ORM\Column(name="identidade")  
    */
    protected $identidade;
    
    
    /** 
    * @ORM\Column(name="numvisits")  
    */
    protected $numvisits;
    
    
    /** 
    * @ORM\Column(name="contact")  
    */
    protected $contact;
    
    
    /**
     * Constructor.
     */
    public function __construct() 
    {
              
    }
    
    
     /**
     * Returns ID of this post.
     * @return integer
     */
    public function getId() 
    {
        return $this->id;
    }

    /**
     * Sets ID of this post.
     * @param int $id
     */
    public function setId($id) 
    {
        $this->id = $id;
    }
    
    
     /**
     * Returns title.
     * @return string
     */
    public function getTitle() 
    {
        return $this->title;
    }

    /**
     * Sets title.
     * @param string $title
     */
    public function setTitle($title) 
    {
        $this->title = $title;
    }
    
    
     /**
     * Returns status.
     * @return integer
     */
    public function getStatus() 
    {
        return $this->status;
    }

    /**
     * Sets status.
     * @param integer $status
     */
    public function setStatus($status) 
    {
        $this->status = $status;
    } 
    
    
     /**
     * Returns post content.
     */
    public function getContent() 
    {
       return $this->content; 
    }
    
    /**
     * Sets post content.
     * @param type $content
     */
    public function setContent($content) 
    {
        $this->content = $content;
    }
    
     /**
     * Returns the date when this post was created.
     * @return string
     */
    public function getDateCreated() 
    {
        return $this->dateCreated;
    }
    
    /**
     * Sets the date when this post was created.
     * @param string $dateCreated
     */
    public function setDateCreated($dateCreated) 
    {
        $this->dateCreated = $dateCreated;
    }
    
    
    /**
     * Returns status.
     * @return string
     */
    public function getIdentidade() 
    {
        return $this->identidade;
    }

    /**
     * Sets status.
     * @param string $identidade
     */
    public function setIdentidade($identidade) 
    {
        $this->identidade = $identidade;
    }
    
     /**
     * Returns identidade.
     * @return integer
     */
    public function getNumvisits() 
    {
        return $this->numvisits;
    }

    /**
     * Sets status.
     * @param integer $numvisits
     */
    public function setNumvisits($numvisits) 
    {
        $this->numvisits = $numvisits;
    }
    
    
    /**
     * Returns the date when this post was created.
     * @return string
     */
    public function getContact() 
    {
        return $this->contact;
    }
    
    /**
     * Sets the date when this post was created.
     * @param string $contact
     */
    public function setContact($contact) 
    {
        $this->contact = $contact;
    }
    
    
    
}