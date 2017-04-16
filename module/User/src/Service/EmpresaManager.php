<?php
namespace User\Service;



use User\Entity\Empresas;
use Zend\Crypt\Password\Bcrypt;
use Zend\Math\Rand;


/**
 * This service is responsible for adding/editing users
 * and changing user password.
 */
class EmpresaManager
{
    /**
     * Doctrine entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;  
    
    private $email;
    
    /**
     * Constructs the service.
     */
    public function __construct($entityManager,$mailtransport) 
    {
        $this->entityManager = $entityManager;
        $this->email = $mailtransport;
    }
    
    /**
     * This method adds a new user.
     */
    public function addUser($data) 
    {
        // Do not allow several users with the same email address.
        if($this->checkUserExists($data['email'])) {
            throw new \Exception("Company with email address " . $data['email'] . " already exists");
        }
        
        // Create new User entity.
        $user = new Empresas();
        $user->setId(uniqid('empresa_'));
        $user->setEmail($data['email']);
        $user->setDesignacao($data['designacao']);        

        // Encrypt password and store the password in encrypted state.
        $bcrypt = new Bcrypt();
        $passwordHash = $bcrypt->create($data['password']);        
        $user->setPassword($passwordHash);
        
        $user->setStatus($data['status']);
        $user->setPerfil($data['perfil']); 
        
        $currentDate = date('Y-m-d H:i:s');
        $user->setDateCreated($currentDate);        
                
        // Add the entity to the entity manager.
        $this->entityManager->persist($user);
        
        // Apply changes to database.
        $this->entityManager->flush();
        
        return $user;
    }
    
    /**
     * This method updates data of an existing user.
     */
    public function updateUser($user, $data) 
    {
        // Do not allow to change user email if another user with such email already exits.
        if($user->getEmail()!=$data['email'] && $this->checkUserExists($data['email'])) {
            throw new \Exception("Another user with email address " . $data['email'] . " already exists");
        }
        
        $user->setEmail($data['email']);
        $user->setFullName($data['full_name']);        
        $user->setStatus($data['status']);   
        $user->setPerfil($data['perfil']); 
        
        // Apply changes to database.
        $this->entityManager->flush();

        return true;
    }
    
    /**
     * This method checks if at least one user presents, and if not, creates 
     * 'Admin' user with email 'admin@example.com' and password 'Secur1ty'. 
     */
    public function createAdminUserIfNotExists()
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy([]);
        if ($user==null) {
            $user = new User();
            $user->setId(uniqid('user_'));
            $user->setEmail('admin@example.com');
            $user->setFullName('Admin');
            $bcrypt = new Bcrypt();
            $passwordHash = $bcrypt->create('Security');        
            $user->setPassword($passwordHash);
            $user->setStatus(User::STATUS_ACTIVE);
            $user->setPerfil(User::PERFIL_ADMIN); 
            $user->setDateCreated(date('Y-m-d H:i:s'));
            
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
    }
    
    /**
     * Checks whether an active user with given email address already exists in the database.     
     */
    public function checkUserExists($email) {
        
        $user = $this->entityManager->getRepository(User::class)
                ->findOneByEmail($email);
        
        return $user !== null;
    }
    
    /**
     * Checks that the given password is correct.
     */
    public function validatePassword($user, $password) 
    {
        $bcrypt = new Bcrypt();
        $passwordHash = $user->getPassword();
        
        if ($bcrypt->verify($password, $passwordHash)) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Generates a password reset token for the user. This token is then stored in database and 
     * sent to the user's E-mail address. When the user clicks the link in E-mail message, he is 
     * directed to the Set Password page.
     */
    public function generatePasswordResetToken($user)
    {
        // Generate a token.
        $token = Rand::getString(32, '0123456789abcdefghijklmnopqrstuvwxyz', true);
        $user->setPasswordResetToken($token);
        
        $currentDate = date('Y-m-d H:i:s');
        $user->setPasswordResetTokenCreationDate($currentDate);  
        
        $this->entityManager->flush();
        
        $subject = 'Password Reset';
            
        $httpHost = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'localhost';
        $passwordResetUrl = 'http://' . $httpHost . '/set-password/' . $token;
        
        $body = 'Please follow the link below to reset your password:\n';
        $body .= "$passwordResetUrl\n";
        $body .= "If you haven't asked to reset your password, please ignore this message.\n";
        
        // Send email to user.
        //mail($user->getEmail(), $subject, $body);
        
        //$this->respondeaauncioEmail($user->getEmail(), 5,$user->getEmail(),$subject,$body);
        $i=array('email' => $user->getEmail(),'token' => $token);
         
        return $i;
    }
    
    /**
     * Checks whether the given password reset token is a valid one.
     */
    public function validatePasswordResetToken($passwordResetToken)
    {
        $user = $this->entityManager->getRepository(User::class)
                ->findOneByPasswordResetToken($passwordResetToken);
        
        if($user==null) {
            return false;
        }
        
        $tokenCreationDate = $user->getPasswordResetTokenCreationDate();
        $tokenCreationDate = strtotime($tokenCreationDate);
        
        $currentDate = strtotime('now');
        
        if ($currentDate - $tokenCreationDate > 24*60*60) {
            return false; // expired
        }
        
        return true;
    }
    
    /**
     * This method sets new password by password reset token.
     */
    public function setNewPasswordByToken($passwordResetToken, $newPassword)
    {
      //  if (!$this->validateResetPasswordUid($passwordResetToken)) {
        if (!$this->validatePasswordResetToken($passwordResetToken)) {
           return false; 
        }
        
        $user = $this->entityManager->getRepository('\User\Entity\User')
                ->findOneBy(array('passwordResetToken'=>$passwordResetToken));
        
        if ($user===null) {
            return false;
        }
                
        // Set new password for user        
        $bcrypt = new Bcrypt();
        $passwordHash = $bcrypt->create($newPassword);        
        $user->setPassword($passwordHash);
                
        // Remove UID
        $user->setPasswordResetToken(null);
        $user->setPasswordResetTokenCreationDate(null);
        
        $this->entityManager->flush();
        
        return true;
    }
    
    /**
     * This method is used to change the password for the given user. To change the password,
     * one must know the old password.
     */
    public function changePassword($user, $data)
    {
        $oldPassword = $data['old_password'];
        
        // Check that old password is correct
        if (!$this->validatePassword($user, $oldPassword)) {
            return false;
        }                
        
        $newPassword = $data['new_password'];
        
        // Check password length
        if (strlen($newPassword)<6 || strlen($newPassword)>64) {
            return false;
        }
        
        // Set new password for user        
        $bcrypt = new Bcrypt();
        $passwordHash = $bcrypt->create($newPassword);
        $user->setPassword($passwordHash);
        
        // Apply changes
        $this->entityManager->flush();

        return true;
    }
    
    
        /**
     * This method is used to change the password for the given user. To change the password,
     * one must know the old password.
     */
    public function changePasswordAdmin($user, $data)
    {
       
        
        $newPassword = $data['new_password'];
        
        // Check password length
        if (strlen($newPassword)<6 || strlen($newPassword)>64) {
            return false;
        }
        
        // Set new password for user        
        $bcrypt = new Bcrypt();
        $passwordHash = $bcrypt->create($newPassword);
        $user->setPassword($passwordHash);
        
        // Apply changes
        $this->entityManager->flush();

        return true;
    }
        /**
     * This method adds a new user.
     */
    public function addregisterUser($data) 
    {
        // Do not allow several users with the same email address.
        if($this->checkUserExists($data['email'])) {
            throw new \Exception("User with email address " . $data['$email'] . " already exists");
        }
        
        // Create new User entity.
        $user = new User();
        $user->setId(uniqid('user_'));
        $user->setEmail($data['email']);
        $user->setFullName($data['full_name']);        

        // Encrypt password and store the password in encrypted state.
        $bcrypt = new Bcrypt();
        $passwordHash = $bcrypt->create($data['password']);        
        $user->setPassword($passwordHash);
        
        $user->setStatus(User::STATUS_RETIRED);
        $user->setPerfil(User::PERFIL_MEMBER); 
        
        $currentDate = date('Y-m-d H:i:s');
        $user->setDateCreated($currentDate);        
                
        // Add the entity to the entity manager.
        $this->entityManager->persist($user);
        
        // Apply changes to database.
        $this->entityManager->flush();
        
        return $user;
    }
   
    
    
       /**
     * Generates a password reset token for the user. This token is then stored in database and 
     * sent to the user's E-mail address. When the user clicks the link in E-mail message, he is 
     * directed to the Set Password page.
     */
    public function generateRegisterResetToken($user)
    {
        // Generate a token.
        $token = Rand::getString(32, '0123456789abcdefghijklmnopqrstuvwxyz', true);
        
        $user->setRegisterToken($token);
        
        $currentDate = date('Y-m-d H:i:s');
        $user->setRegisterTokenCreationDate($currentDate);  
        
        $this->entityManager->flush();
        
        
        $i=array('email' => $user->getEmail(),'token' => $token);
         
        return $i;
    }
    
        /**
     * Checks whether the given register token is a valid one.
     */
    public function validateRegisterToken($registerToken)
    {
        $user = $this->entityManager->getRepository(User::class)
                ->findOneByRegisterToken($registerToken);
        
        if($user==null) {
            return false;
        }
        
//        $tokenCreationDate = $user->getRegisterTokenCreationDate();
//        $tokenCreationDate = strtotime($tokenCreationDate);
//        
//        $currentDate = strtotime('now');
//        
//        if ($currentDate - $tokenCreationDate > 24*60*60) {
//            return false; // expired
//        }
        
        return true;
    }
    
    
     ////////////////////setRegisteryToken
     /**
     * This method sets new password by password reset token.
     */
    
    public function setRegisteryToken($registerToken)
    {
      //  if (!$this->validateResetPasswordUid($passwordResetToken)) {
        if (!$this->validateRegisterToken($registerToken)) {
           return false; 
        }
        
        $user = $this->entityManager->getRepository('\User\Entity\User')
                ->findOneBy(array('registerToken'=>$registerToken));
        
        if ($user===null) {
            return false;
        }
                
          
             
        $user->setStatus(User::STATUS_ACTIVE);
        
        
                
        // Remove UID
        $user->setRegisterToken(null);
        $user->setRegisterTokenCreationDate(null);
        
        $this->entityManager->flush();
        
        return true;
    }
    

    public function setProexpireDate($date,$userid)
    {
       
        
        $user = $this->entityManager->getRepository('\User\Entity\User')
                ->findOneById($userid);
        
        if ($user===null) {
            return false;
        }
                                       
        $user->setPerfil(User::PERFIL_PRO);
        $user->setProexpiredate($date);
        
        
        $this->entityManager->flush();
        
        return true;
    }
}

