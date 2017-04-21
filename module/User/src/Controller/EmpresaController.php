<?php
namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

use User\Entity\Empresas;
use User\Entity\User;
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
     * Empresa manager.
     * @var User\Service\EmpresaManager 
     */
    private $userManager;
    
    private $mailtransport;
    
    private $translator;
    
    private $userImageManager;
    
    
                /**
     * Authentication service.
     * @var \Zend\Authentication\AuthenticationService
     */
    private $authService;
    /**
     * Constructor. 
     */
    public function __construct($entityManager, $userManager, $mailtransport,$translator,$userImageManager,$authenticationService)
    {
        $this->entityManager = $entityManager;
        $this->userManager = $userManager;
        $this->mailtransport = $mailtransport;
         $this->translator = $translator;
        $this->userImageManager = $userImageManager;
        $this->authService = $authenticationService;
    }
    

    
    /**
     * This action displays a page allowing to add a new user.
     */
    //ja esta
    public function addAction()
    {
           $this->traduz();
        // Create user form
        $form = new EmpresasForm('create', $this->entityManager);
        
        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {
            
            // Fill in the form with POST data
            $data = $this->params()->fromPost();            
            
            $form->setData($data);
            
            // Validate form
            if($form->isValid()) {
                
                // Get filtered and validated data
                $data = $form->getData();
                
                // Add company.
                $user = $this->userManager->addUser($data);
                
                // $this->flashMessenger()->addMessage('Added user successfully.');
                 
                 
                
                // Redirect to "view" page
                return $this->redirect()->toRoute('empresas', 
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
        $empresa = $this->entityManager->getRepository(Empresas::class)
                ->find($id);
        
        if ($empresa == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

                
        return new ViewModel([
            'empresa' => $empresa,
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
        
        $empresa = $this->entityManager->getRepository(Empresas::class)
                ->find($id);
        
        if ($empresa == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        // Create user form
        $form = new EmpresasForm('update', $this->entityManager, $empresa);
        
        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {
            
            // Fill in the form with POST data
            $data = $this->params()->fromPost();            
            
            $form->setData($data);
            
            // Validate form
            if($form->isValid()) {
                
                // Get filtered and validated data
                $data = $form->getData();
                
                // Update the empresa.
                $this->userManager->updateUser($empresa, $data);
                
                // Redirect to "view" page
                return $this->redirect()->toRoute('empresas', 
                        ['action'=>'view', 'id'=>$empresa->getId()]);                
            }               
        } else {
            $form->setData(array(
                'email'=>$empresa->getEmail(),
                'designacao'=>$empresa->getDesignacao(),
                'descricao'=>$empresa->getDescricao(),
                'endereco'=>$empresa->getEndereco(),
                'site'=>$empresa->getSite(),
                'facebook'=>$empresa->getFacebook(),
                'linkedin'=>$empresa->getLinkedin(),
                'twitter'=>$empresa->getTwitter(),
                'telefone'=>$empresa->getTelefone(),
                'fax'=>$empresa->getFax(),    
                ));
        }
        
        return new ViewModel(array(
            'empresa' => $empresa,
            'form' => $form
        ));
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
     * This "delete" action deletes the given post.
     */
    public function deleteAction()
    {
        
        
        $postId = $this->params()->fromRoute('id', -1);
        
        // Validate input parameter
        if ($postId<0) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        $post = $this->entityManager->getRepository(Empresas::class)
                ->findOneById($postId);        
        if ($post == null) {
            $this->getResponse()->setStatusCode(404);
            return;                        
        }        
        
        $this->userManager->removePost($post);
        $user = $this->entityManager->getRepository(User::class)
                        ->findOneByEmail($this->authService->getIdentity());
        if($user->getPerfil()==User::PERFIL_ADMIN)
        {

       
            
                       return $this->redirect()->toRoute('users', 
                        ['action'=>'view', 'id'=>$user->getId()]);  
        }else
        {
              return $this->redirect()->toRoute('application', 
                        ['action'=>'settings']);


        }      
                
    }
    
   
    
}

