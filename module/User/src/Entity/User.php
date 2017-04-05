<?php
namespace User\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This class represents a registered user.
 * @ORM\Entity()
 * @ORM\Table(name="user")
 */
class User 
{
    // User status constants.
    const STATUS_ACTIVE       = 1; // Active user.
    const STATUS_RETIRED      = 2; // Retired user.
    
    
    // User perfil constants.
    const PERFIL_MEMBER       = 0; // Member user.
    const PERFIL_ADMIN        = 1; // Admin user.
    const PERFIL_PRO        = 2; // PRO user.
    
//    /**
//     * @ORM\Id
//     * @ORM\Column(name="id")
//     * @ORM\GeneratedValue
//     */
//    protected $id;
    
    /**
     * @ORM\Id
     * @ORM\Column(name="id")
     */
    protected $id;

    /** 
     * @ORM\Column(name="email")  
     */
    protected $email;
    
    /** 
     * @ORM\Column(name="full_name")  
     */
    protected $fullName;

    /** 
     * @ORM\Column(name="password")  
     */
    protected $password;

    /** 
     * @ORM\Column(name="status")  
     */
    protected $status;
    
    /**
     * @ORM\Column(name="date_created")  
     */
    protected $dateCreated;
        
    /**
     * @ORM\Column(name="pwd_reset_token")  
     */
    protected $passwordResetToken;
    
    /**
     * @ORM\Column(name="pwd_reset_token_creation_date")  
     */
    protected $passwordResetTokenCreationDate;
    
    
    /**
     * @ORM\Column(name="register_token")  
     */
    protected $registerToken;
    
    /**
     * @ORM\Column(name="register_token_creation_date")  
     */
    protected $registerTokenTokenCreationDate;
    
    /** 
    * @ORM\Column(name="perfil")  
    */
    protected $perfil;
    
    /** 
    * @ORM\Column(name="unsubscribe")  
    */
    protected $unsubscribe;
    
    /**
    * @ORM\Column(name="pro_expire_date")  
    */
    protected $pro_expire_date;
    
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
     * Returns full name.
     * @return string     
     */
    public function getFullName() 
    {
        return $this->fullName;
    }       

    /**
     * Sets full name.
     * @param string $fullName
     */
    public function setFullName($fullName) 
    {
        $this->fullName = $fullName;
    }
    
    /**
     * Returns status.
     * @return int     
     */
    public function getStatus() 
    {
        return $this->status;
    }
    
    /**
     * Returns status.
     * @return int     
     */
    public function getPerfil() 
    {
        return $this->perfil;
    }
    
    
        /**
     * Returns possible statuses as array.
     * @return array
     */
    public static function getPerfilList() 
    {
        return [
            self::PERFIL_MEMBER => 'Member',
            self::PERFIL_ADMIN => 'Admin',
            self::PERFIL_PRO => 'Professional'
        ];
    } 

        /**
     * Returns user status as string.
     * @return string
     */
    public function getPerfilAsString()
    {
        $list = self::getPerfilList();
        if (isset($list[$this->perfil]))
            return $list[$this->perfil];
        
        return 'Unknown';
    }  
    /**
     * Returns possible statuses as array.
     * @return array
     */
    public static function getStatusList() 
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_RETIRED => 'Retired'
        ];
    }    
    
        /**
     * Sets status.
     * @param int $perfil    
     */
    public function setPerfil($perfil) 
    {
        $this->perfil = $perfil;
    }   
    
    /**
     * Returns user status as string.
     * @return string
     */
    public function getStatusAsString()
    {
        $list = self::getStatusList();
        if (isset($list[$this->status]))
            return $list[$this->status];
        
        return 'Unknown';
    }    
    
    /**
     * Sets status.
     * @param int $status     
     */
    public function setStatus($status) 
    {
        $this->status = $status;
    }   
    
    /**
     * Returns password.
     * @return string
     */
    public function getPassword() 
    {
       return $this->password; 
    }
    
    /**
     * Sets password.     
     * @param string $password
     */
    public function setPassword($password) 
    {
        $this->password = $password;
    }
    
    /**
     * Returns the date of user creation.
     * @return string     
     */
    public function getDateCreated() 
    {
        return $this->dateCreated;
    }
    
    /**
     * Sets the date when this user was created.
     * @param string $dateCreated     
     */
    public function setDateCreated($dateCreated) 
    {
        $this->dateCreated = $dateCreated;
    }    
    
    /**
     * Returns password reset token.
     * @return string
     */
    public function getResetPasswordToken()
    {
        return $this->passwordResetToken;
    }
    
    /**
     * Sets password reset token.
     * @param string $token
     */
    public function setPasswordResetToken($token) 
    {
        $this->passwordResetToken = $token;
    }
    
    /**
     * Returns password reset token's creation date.
     * @return string
     */
    public function getPasswordResetTokenCreationDate()
    {
        return $this->passwordResetTokenCreationDate;
    }
    
    /**
     * Sets password reset token's creation date.
     * @param string $date
     */
    public function setPasswordResetTokenCreationDate($date) 
    {
        $this->passwordResetTokenCreationDate = $date;
    }
    
    /////////////////REGISTER
    
    /**
     * Returns password reset token.
     * @return string
     */
    public function getRegisterToken()
    {
        return $this->registerToken;
    }
    
    /**
     * Sets password reset token.
     * @param string $token
     */
    public function setRegisterToken($token) 
    {
        $this->registerToken = $token;
    }
    
    /**
     * Returns password reset token's creation date.
     * @return string
     */
    public function getRegisterTokenCreationDate()
    {
        return $this->registerTokenTokenCreationDate;
    }
    
    /**
     * Sets password reset token's creation date.
     * @param string $date
     */
    public function setRegisterTokenCreationDate($date) 
    {
        $this->registerTokenTokenCreationDate = $date;
    }
    
    
    
     /**
     * Sets unsubscribe.
     * @param int $unsubscribe    
     */
    public function setUnsubscribe($unsubscribe) 
    {
        $this->unsubscribe = $unsubscribe;
    } 
    
    
     /**
     * Returns unsubscribe.
     * @return int     
     */
    public function getUnsubscribe() 
    {
        return $this->unsubscribe;
    }
    
        /**
     * Returns the date of user creation.
     * @return string     
     */
    public function getProexpiredate() 
    {
        return $this->pro_expire_date;
    }
    
    /**
     * Sets the date when this user was created.
     * @param string $dateCreated     
     */
    public function setProexpiredate($pro_expire_date) 
    {
        $this->pro_expire_date = $pro_expire_date;
    }  
}



