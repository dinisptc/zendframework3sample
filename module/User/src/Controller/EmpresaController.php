<?php
namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

use User\Entity\Empresas;
use User\Form\EmpresasForm;
use User\Form\RegisterForm;
use User\Form\PasswordChangeForm;
use User\Form\PasswordResetForm;

use Zend\Session\Container; // We need this when using sessions
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;
/**
 * This controller is responsible for user management (adding, editing, 
 * viewing users and changing user's password).
 */
class EmpresaController extends AbstractActionController 
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;
    
    /**
     * User manager.
     * @var User\Service\UserManager 
     */
    private $userManager;
    
    private $mailtransport;
    
    private $translator;
    
    private $userImageManager;
    
    /**
     * Constructor. 
     */
    public function __construct($entityManager, $userManager, $mailtransport,$translator,$userImageManager)
    {
        $this->entityManager = $entityManager;
        $this->userManager = $userManager;
        $this->mailtransport = $mailtransport;
         $this->translator = $translator;
        $this->userImageManager = $userImageManager;
    }
    
    /**
     * This is the default "index" action of the controller. It displays the 
     * list of users.
     */
    public function indexAction() 
    {
       
        $this->traduz();
           
        $page = $this->params()->fromQuery('page', 1);

        
        $users = $this->entityManager->createQuery("SELECT u FROM User\Entity\Empresas u order by u.dateCreated DESC");
        
        $adapter = new DoctrineAdapter(new ORMPaginator($users, false));
        $paginator = new Paginator($adapter);
        $paginator->setDefaultItemCountPerPage(10);        
        $paginator->setCurrentPageNumber($page);
        
        return new ViewModel([
            'users' => $paginator,
            'entitymanager'=>$this->entityManager,
        ]);
    } 
    
    /**
     * This action displays a page allowing to add a new user.
     */
    public function addAction()
    {
           $this->traduz();
        // Create user form
        $form = new UserForm('create', $this->entityManager);
        
        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {
            
            // Fill in the form with POST data
            $data = $this->params()->fromPost();            
            
            $form->setData($data);
            
            // Validate form
            if($form->isValid()) {
                
                // Get filtered and validated data
                $data = $form->getData();
                
                // Add user.
                $user = $this->userManager->addUser($data);
                
                // $this->flashMessenger()->addMessage('Added user successfully.');
                 
                 
                
                // Redirect to "view" page
                return $this->redirect()->toRoute('users', 
                        ['action'=>'view', 'id'=>$user->getId()]);                
            }               
        } 
        
        return new ViewModel([
                'form' => $form
            ]);
    }
    
    /**
     * The "view" action displays a page allowing to view user's details.
     */
    public function viewAction() 
    {
           $this->traduz();
        $id = $this->params()->fromRoute('id', -1);
        if ($id<0) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        // Find a user with such ID.
        $user = $this->entityManager->getRepository(User::class)
                ->find($id);
        
        if ($user == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

                
        return new ViewModel([
            'user' => $user,
           'flash'=>$this->flashMessenger()->getMessages(),
                  'userImageManager'=>$this->userImageManager,
            
            
        ]);
    }
    
    /**
     * The "edit" action displays a page allowing to edit user.
     */
    public function editAction() 
    {
           $this->traduz();
        $id = $this->params()->fromRoute('id', -1);
        if ($id<0) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        $user = $this->entityManager->getRepository(User::class)
                ->find($id);
        
        if ($user == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        // Create user form
        $form = new UserForm('update', $this->entityManager, $user);
        
        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {
            
            // Fill in the form with POST data
            $data = $this->params()->fromPost();            
            
            $form->setData($data);
            
            // Validate form
            if($form->isValid()) {
                
                // Get filtered and validated data
                $data = $form->getData();
                
                // Update the user.
                $this->userManager->updateUser($user, $data);
                
                // Redirect to "view" page
                return $this->redirect()->toRoute('users', 
                        ['action'=>'view', 'id'=>$user->getId()]);                
            }               
        } else {
            $form->setData(array(
                    'full_name'=>$user->getFullName(),
                    'email'=>$user->getEmail(),
                    'status'=>$user->getStatus(),                    
                ));
        }
        
        return new ViewModel(array(
            'user' => $user,
            'form' => $form
        ));
    }
    
    /**
     * This action displays a page allowing to change user's password.
     */
    public function changePasswordAction() 
    {
        
           $this->traduz();
        $id = $this->params()->fromRoute('id', -1);
        if ($id<0) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        $user = $this->entityManager->getRepository(User::class)
                ->find($id);
        
        if ($user == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        // Create "change password" form
        $form = new PasswordChangeForm('change');
        
        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {
            
            // Fill in the form with POST data
            $data = $this->params()->fromPost();            
            
            $form->setData($data);
            
            // Validate form
            if($form->isValid()) {
                
                // Get filtered and validated data
                $data = $form->getData();
                
                // Try to change password.
                if (!$this->userManager->changePassword($user, $data)) {
                    $this->flashMessenger()->addMessage(
                            _('Sorry, the old password is incorrect. Could not set the new password.'));
                } else {
                    $this->flashMessenger()->addMessage(
                            _('Changed the password successfully.'));
                }
                
                // Redirect to "view" page
                return $this->redirect()->toRoute('application', 
                        ['action'=>'settings', 'id'=>$user->getId(), 'flash'=>$this->flashMessenger()->getMessages()]);                
            }               
        } 
        
        return new ViewModel([
            'user' => $user,
            'form' => $form,
        
        ]);
    }
    
    /**
     * This action displays the "Reset Password" page.
     */
    public function resetPasswordAction()
    {
        
           $this->traduz();
        // Create form
        $form = new PasswordResetForm();
        
        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {
            
            // Fill in the form with POST data
            $data = $this->params()->fromPost();            
            
            $form->setData($data);
            
            // Validate form
            if($form->isValid()) {
                
                // Look for the user with such email.
                $user = $this->entityManager->getRepository(User::class)
                        ->findOneByEmail($data['email']);                
                if ($user!=null) {
                    // Generate a new password for user and send an E-mail 
                    // notification about that.
                    $m=$this->userManager->generatePasswordResetToken($user);
                    
                    $re=$this->emailtokenresetpassword($m['email'],$m['token']);
                    
                    
                    if($re==1)
                    {
                    // Redirect to "message" page
                    return $this->redirect()->toRoute('users', 
                            ['action'=>'message', 'id'=>'sent']); 
                    
                    }elseif($re==0){
                           // Redirect to "message" page
                    return $this->redirect()->toRoute('users', 
                            ['action'=>'message', 'id'=>'failed']);
                        
                    }
                } else {
                    return $this->redirect()->toRoute('users', 
                            ['action'=>'message', 'id'=>'invalid-email']);                 
                }
            }               
        } 
        
        return new ViewModel([                    
            'form' => $form
        ]);
    }
    
    /**
     * This action displays an informational message page. 
     * For example "Your password has been resetted" and so on.
     */
    public function messageAction() 
    {
        
           $this->traduz();
        // Get message ID from route.
        $id = (string)$this->params()->fromRoute('id');
        
        // Validate input argument.
        if($id!='invalid-email' && $id!='sent' && $id!='set' && $id!='failed') {
            throw new \Exception('Invalid message ID specified');
        }
        
        return new ViewModel([
            'id' => $id
        ]);
    }
    
    /**
     * This action displays the "Reset Password" page. 
     */
    public function setPasswordAction()
    {
        
           $this->traduz();
        $token = $this->params()->fromRoute('token', null);
        
        // Validate token length
        if ($token!=null && (!is_string($token) || strlen($token)!=32)) {
            throw new \Exception('Invalid token type or length');
        }
        
        if($token===null || 
           !$this->userManager->validatePasswordResetToken($token)) {
            return $this->redirect()->toRoute('users', 
                    ['action'=>'message', 'id'=>'failed']);
        }
                
        // Create form
        $form = new PasswordChangeForm('reset');
        
        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {
            
            // Fill in the form with POST data
            $data = $this->params()->fromPost();            
            
            $form->setData($data);
            
            // Validate form
            if($form->isValid()) {
                
                $data = $form->getData();
                                               
                // Set new password for the user.
                //if ($this->userManager->setPasswordByToken($token, $data['password'])) {
                if ($this->userManager->setNewPasswordByToken($token, $data['new_password'])) {
                    
                    // Redirect to "message" page
                    return $this->redirect()->toRoute('users', 
                            ['action'=>'message', 'id'=>'set']);                 
                } else {
                    // Redirect to "message" page
                    return $this->redirect()->toRoute('users', 
                            ['action'=>'message', 'id'=>'failed']);                 
                }
            }               
        } 
        
        return new ViewModel([                    
            'form' => $form
        ]);
    }
    
   

    public function emailtokenresetpassword($usr_email,$token)
{
    
    
      
        $subject = 'Password Reset - www.etiju.com';
            
        $httpHost = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'localhost';
        $passwordResetUrl = 'http://' . $httpHost . '/set-password/' . $token;
        
//        $body = 'Please follow the link below to reset your password:\n';
//        $body .= "$passwordResetUrl\n";
//        $body .= "If you haven't asked to reset your password, please ignore this message.\n";
//    
    		$transport = $this->mailtransport; //$this->getServiceLocator()->get('mail.transport');
		$message = new Message();
		$this->getRequest()->getServer();  //Server vars
                $htmlMarkup='Please follow the link below to reset your password: <br><br> '.$passwordResetUrl.' <br><br> If did not asked to reset your password, please ignore this message.';
                $html = new MimePart($htmlMarkup);
                $html->type = "text/html";
                $body = new MimeMessage();
                $body->setParts(array($html));
		$message->addTo($usr_email)
				->addFrom('dinisnet@hotmail.com')
				->setSubject($subject)
				->setBody($body);
                
                //Send it!
                $sent = true;
                try {
                    $transport->send($message);
                } catch (Exception $e){
                    $sent = false;
                }

                //Do stuff (display error message, log it, redirect user, etc)
                if($sent){
                    return 1;
                } else {
                    //Mail failed to send.
                    return 0;
                }
    
                
    
    
    
    }
    
    
    private function traduz()
    {
        
        $user_session = new Container('language');
        $lang = $user_session->lang;
        
        $translator = $this->translator;//$this->getServiceLocator()->get('translator');
        
        if (($lang=='') || ($lang==null))
        {
          $translator->setLocale(\Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']));
            
        }else
        {
            
         $translator->setLocale($user_session->lang);
        }
        
    }
    
    
    
    /**
    * This action displays a page allowing to register a new user.
    */
    public function registerAction()
    {
        $this->traduz();
        // Create user form
        $form = new RegisterForm('register', $this->entityManager);
        
        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {
            
            // Fill in the form with POST data
            $data = $this->params()->fromPost();            
            
            $form->setData($data);
            
            // Validate form
            if($form->isValid()) {
                
                // Get filtered and validated data
                $data = $form->getData();
                
                 // Add user.
                $user = $this->userManager->addregisterUser($data);
                
                      // Look for the user with such email.
                $user = $this->entityManager->getRepository(User::class)
                        ->findOneByEmail($data['email']);                
                if ($user!=null) {
                    // Generate a new password for user and send an E-mail 
                    // notification about that.
                    $m=$this->userManager->generateRegisterResetToken($user);
                  
                    $re=$this->emailtokenregister($m['email'],$m['token']);
                    
                    
                    if($re==1)
                    {
                    // Redirect to "message" page
                    return $this->redirect()->toRoute('users', 
                            ['action'=>'messageregistration', 'id'=>'sent']); 
                    
                    }elseif($re==0){
                           // Redirect to "message" page
                    return $this->redirect()->toRoute('users', 
                            ['action'=>'messageregistration', 'id'=>'failed']);
                        
                    }
                } else {
                    return $this->redirect()->toRoute('users', 
                            ['action'=>'messageregistration', 'id'=>'invalid-email']);                 
                }
                
                
               
                
               
                 
                 
                
                // Redirect to "view" page
                return $this->redirect()->toRoute('users', 
                        ['action'=>'view', 'id'=>$user->getId()]);                
            }               
        } 
        
        return new ViewModel([
                'form' => $form
            ]);
    }
    
    
        public function emailtokenregister($usr_email,$token)
{
    
    
      
        $subject = 'Register - www.etiju.com';
            
        $httpHost = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'localhost';
        $passwordResetUrl = 'http://' . $httpHost . '/set-register/' . $token;
        

    		$transport = $this->mailtransport; //$this->getServiceLocator()->get('mail.transport');
		$message = new Message();
		$this->getRequest()->getServer();  //Server vars
                $htmlMarkup='Please follow the link below to complete you registration: <br><br> '.$passwordResetUrl.' <br><br> If you did not register, please ignore this message.';
                $html = new MimePart($htmlMarkup);
                $html->type = "text/html";
                $body = new MimeMessage();
                $body->setParts(array($html));
		$message->addTo($usr_email)
				->addFrom('dinisnet@hotmail.com')
				->setSubject($subject)
				->setBody($body);
                
                //Send it!
                $sent = true;
                try {
                    $transport->send($message);
                } catch (Exception $e){
                    $sent = false;
                }

                //Do stuff (display error message, log it, redirect user, etc)
                if($sent){
                    return 1;
                } else {
                    //Mail failed to send.
                    return 0;
                }
    
                
    
    
    
    }
    
    
    
        /**
     * This action displays the "Set Register" page. 
     */
    public function setRegisterAction()
    {
        
           $this->traduz();
        $token = $this->params()->fromRoute('token', null);
        
        // Validate token length
        if ($token!=null && (!is_string($token) || strlen($token)!=32)) {
            throw new \Exception('Invalid token type or length');
        }
        
        if($token===null || 
           !$this->userManager->validateRegisterToken($token)) {
            return $this->redirect()->toRoute('users', 
                    ['action'=>'messageregistration', 'id'=>'failed']);
        }
                
        
                if ($this->userManager->setRegisteryToken($token)) {
                    
                    // Redirect to "message" page
                    return $this->redirect()->toRoute('users', 
                            ['action'=>'messageregistration', 'id'=>'set']);                 
                } else {
                    // Redirect to "message" page
                    return $this->redirect()->toRoute('users', 
                            ['action'=>'messageregistration', 'id'=>'failed']);                 
                }
     
        return new ViewModel([                    
            'form' => $form
        ]);
    
    
}


    /**
     * This action displays an informational message page. 
     * For example "Your password has been resetted" and so on.
     */
    public function messageregistrationAction() 
    {
        
           $this->traduz();
        // Get message ID from route.
        $id = (string)$this->params()->fromRoute('id');
        
        // Validate input argument.
        if($id!='invalid-email' && $id!='sent' && $id!='set' && $id!='failed') {
            throw new \Exception('Invalid message ID specified');
        }
        
        return new ViewModel([
            'id' => $id
        ]);
    }
    
     //unsubscribe
    public function unsubscribeAction()
    {             
        // Get post ID.
        $userId = $this->params()->fromRoute('id', -1);
        
        $mensagem=0;
        
        // Validate input parameter
        if ($userId<0) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
                
        $user = $this->entityManager->getRepository(User::class)->findOneById($userId);
        
        if(isset($user))
        {
            // Create new User entity.
            //$useri = new User();
            $user->setUnsubscribe('1');
            
            // Add the entity to the entity manager.
            //$this->entityManager->persist($useri);
        
            // Apply changes to database.
            $this->entityManager->flush();
            $mensagem=1;
            
            
        }else
        {
            $this->getResponse()->setStatusCode(404);
            return;
            
        }
        
    return array(
        'mensagem'=> $mensagem,
        
    );
     
    }
    
    
        //unsubscribe
    public function subscribeAction()
    {             
        // Get post ID.
        $userId = $this->params()->fromRoute('id', -1);
        
        $mensagem=0;
        
        // Validate input parameter
        if ($userId<0) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
                
        $user = $this->entityManager->getRepository(User::class)->findOneById($userId);
        
        if(isset($user))
        {
            // Create new User entity.
            //$useri = new User();
            $user->setUnsubscribe('0');
            
            // Add the entity to the entity manager.
            //$this->entityManager->persist($useri);
        
            // Apply changes to database.
            $this->entityManager->flush();
            $mensagem=1;
            
            
        }else
        {
            $this->getResponse()->setStatusCode(404);
            return;
            
        }
        
    return array(
        'mensagem'=> $mensagem,
        
    );
     
    } 
    
    
        /**
     * This action displays a page allowing to change user's password.
     */
    public function changePasswordAdminAction() 
    {
        
           $this->traduz();
        $id = $this->params()->fromRoute('id', -1);
        if ($id<0) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        $user = $this->entityManager->getRepository(User::class)
                ->find($id);
        
        if ($user == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        // Create "change password" form
        $form = new PasswordChangeForm('changeadmin');
        
        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {
            
            // Fill in the form with POST data
            $data = $this->params()->fromPost();            
            
            $form->setData($data);
            
            // Validate form
            if($form->isValid()) {
                
                // Get filtered and validated data
                $data = $form->getData();
                
                // Try to change password.
                if (!$this->userManager->changePasswordAdmin($user, $data)) {
                    
                } else {
                    $this->flashMessenger()->addMessage(
                            _('Changed the password successfully.'));
                }
                
                // Redirect to "view" page
                return $this->redirect()->toRoute('users', 
                        ['action'=>'view', 'id'=>$user->getId(), 'flash'=>$this->flashMessenger()->getMessages()]);                
            }               
        } 
        
        return new ViewModel([
            'user' => $user,
            'form' => $form
        ]);
    }

}

