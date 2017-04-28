<?php
namespace Empregos\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Empregos\Form\AutoForm;
use Empregos\Entity\Empregos;
use Empregos\Entity\Msgempregos;


use User\Entity\User;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;


use Zend\Session\Container; // We need this when using sessions
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

/**
 * This is the Post controller class of the Blog application. 
 * This controller is used for managing posts (adding/editing/viewing/deleting).
 */
class EmpregosController extends AbstractActionController 
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager 
     */
    public $entityManager;
    
    /**
     * Post manager.
     * @var Application\Service\AutoManager 
     */
    private $autoManager;
    
            /**
     * Authentication service.
     * @var \Zend\Authentication\AuthenticationService
     */
    private $authService;
    
    
    private $mailtransport;
        
        
    private $translator;
    

    
    private $userManager;
    
    private $userImageManager;
    
    private $empresaImageManager;
    
    /**
     * Constructor is used for injecting dependencies into the controller.
     */
    public function __construct($entityManager, $postManager, $authService, $mailtransport,$translator,$userManager, $userImageManager, $empresaImageManager) 
    {
        $this->entityManager = $entityManager;
        $this->autoManager = $postManager;
        $this->authService = $authService;
        $this->mailtransport = $mailtransport;
        $this->translator = $translator;
    
        $this->userManager =$userManager;
        $this->userImageManager =$userImageManager;
        $this->empresaImageManager =$empresaImageManager;
        
    }
    
    
        /**
     * This action displays the "New Post" page. The page contains a form allowing
     * to enter post title, content and tags. When the user clicks the Submit button,
     * a new Post entity will be created.
     */
    public function addAction() 
    {     
        
        $user = $this->entityManager->getRepository(User::class)
                        ->findOneByEmail($this->authService->getIdentity());
        
        if(isset($user))
        {
            $identidade=$user->getId();
            $perfil=$user->getPerfil();
             $dqlmemb = "SELECT COUNT(p) FROM Empregos\Entity\Empregos p where (p.status='".Empregos::STATUS_PUBLISHED."' or p.status='".Empregos::STATUS_APROVAR."') and p.identidade='".$identidade."'";
            
            $q1 = $this->entityManager->createQuery($dqlmemb);
            $contaparamember = $q1->getSingleScalarResult();
            
              if($contaparamember>=1 && $perfil== User::PERFIL_MEMBER){
                  
                  return $this->redirect()->toRoute('application', ['action'=>'settings']);
              }
            
        }else
        {
            $identidade=null;
            $perfil=null;
        }
        
    
        if($user->getPerfil()==User::PERFIL_ADMIN)
        {
              $form = new AutoForm('admin');  
         }else
         {
              // Create the form.
              $form = new AutoForm();
             
         }
                 
        // Check whether this post is a POST request.
        if ($this->getRequest()->isPost()) {
            
            // Get POST data.
            $data = $this->params()->fromPost();
            
            // Fill form with data.
            $form->setData($data);
            if ($form->isValid()) {
                                
                // Get validated form data.
                $data = $form->getData();
                
                // Use post manager service to add new post to database.                
                $this->autoManager->addNewPost($data);
                
                // Redirect the user to "index" page.
                //return $this->redirect()->toRoute('blog');
                if($user->getPerfil()==User::PERFIL_ADMIN)
                {
             
                    // Redirect the user to "admin" page.
                    return $this->redirect()->toRoute('empregosposts', ['action'=>'admin']);  
                }else
                {
             
                // Redirect the user to "admin" page.
                return $this->redirect()->toRoute('empregosposts', ['action'=>'myadmin']);
             
                }
            }
        }
        
        // Render the view template.
        return new ViewModel([
            'form' => $form
        ]);
    } 
    
        /**
     * This "admin" action displays the Manage Posts page. This page contains
     * the list of posts with an ability to edit/delete any post.
     */
    public function adminAction()
    {
        $page = $this->params()->fromQuery('page', 1);
        

        
        $posts = $this->entityManager->createQuery("SELECT u FROM Empregos\Entity\Empregos u order by u.dateCreated DESC");
        
        $adapter = new DoctrineAdapter(new ORMPaginator($posts, false));
        $paginator = new Paginator($adapter);
        $paginator->setDefaultItemCountPerPage(10);        
        $paginator->setCurrentPageNumber($page);
        
        // Render the view template
        return new ViewModel([
            'posts' => $paginator,
            'postManager' => $this->autoManager
        ]);        
    }
    
    
        /**
     * This "admin" action displays the Manage Posts page. This page contains
     * the list of posts with an ability to edit/delete any post.
     */
    public function myadminAction()
    {
        
        $user = $this->entityManager->getRepository(User::class)
                        ->findOneByEmail($this->authService->getIdentity());
    
           
        $identidade=$user->getId();
        
        
        $page = $this->params()->fromQuery('page', 1);
        
        
        $posts = $this->entityManager->createQuery("SELECT u FROM Empregos\Entity\Empregos u where u.identidade='".$identidade."' order by u.dateCreated DESC");
        
        $adapter = new DoctrineAdapter(new ORMPaginator($posts, false));
        $paginator = new Paginator($adapter);
        $paginator->setDefaultItemCountPerPage(10);        
        $paginator->setCurrentPageNumber($page);
        
        $contaparamember=0;
        
        
    
        if(isset($user))
        {
       
            $perfil=$user->getPerfil();
        
            
            
            $dqlmemb = "SELECT COUNT(p) FROM Empregos\Entity\Empregos p where (p.status='".Empregos::STATUS_PUBLISHED."' or p.status='".Empregos::STATUS_APROVAR."') and p.identidade='".$identidade."'";
            $q1 = $this->entityManager->createQuery($dqlmemb);
            $contaparamember = $q1->getSingleScalarResult();
            
        }else
        {
            $identidade=null;
            $perfil=null;
        }
        
        // Render the view template
        return new ViewModel([
            'posts' => $paginator,
            'postManager' => $this->autoManager,
            'perfil'=>$perfil,
            'contaparamember'=>$contaparamember,
        ]);        
    }
    
    
            /**
     * This "admin" action displays the Manage Posts page. This page contains
     * the list of posts with an ability to edit/delete any post.
     */
    public function adminaprovarAction()
    {
        $page = $this->params()->fromQuery('page', 1);
        

        
        $posts = $this->entityManager->createQuery("SELECT u FROM Empregos\Entity\Empregos u where u.status='".Empregos::STATUS_APROVAR."' order by u.dateCreated DESC");
        
   
        $adapter = new DoctrineAdapter(new ORMPaginator($posts, false));
        $paginator = new Paginator($adapter);
        $paginator->setDefaultItemCountPerPage(10);        
        $paginator->setCurrentPageNumber($page);
        
        
        // Render the view template
        return new ViewModel([
            'posts' => $paginator,
            'postManager' => $this->autoManager
        ]);        
    }
    
    
    
                 /**
     * This "admin" action displays the Manage Posts page. This page contains
     * the list of posts with an ability to edit/delete any post.
     */
    public function adminexpiredAction()
    {
        $page = $this->params()->fromQuery('page', 1);
              
        //get de todos published
        $posts = $this->entityManager->createQuery("SELECT u FROM Empregos\Entity\Empregos u where u.status='".Empregos::STATUS_PUBLISHED."' order by u.dateCreated DESC");
        $posts = $posts->getResult();
        
        
                
        foreach($posts as $post)  
        {
            

           $currentDate = date('Y-m-d H:i:s');                
           $addate=$post->getDateCreated(); 
           
        
           
           $user = $this->entityManager->getRepository(User::class)
                ->findOneById($post->getIdentidade());
           
           
           if($user->getPerfil()==User::PERFIL_MEMBER)
           {
                $addateexpire = strtotime("+3 months", strtotime($addate)); // returns timestamp
                $addateexpire=date('Y-m-d H:i:s',$addateexpire);
           }else
           {
                $addateexpire = strtotime("+12 months", strtotime($addate)); // returns timestamp
                $addateexpire=date('Y-m-d H:i:s',$addateexpire);               
           }
           if($addateexpire<=$currentDate)
           
           {
                         //update os que tiverem expirados
            $this->autoManager->expirepost($post); 
           }

        }
        
        
        $posts = $this->entityManager->createQuery("SELECT u FROM Empregos\Entity\Empregos u where u.status='".Empregos::STATUS_EXPIRED."' order by u.dateCreated DESC");
      
        
        $adapter = new DoctrineAdapter(new ORMPaginator($posts, false));
        $paginator = new Paginator($adapter);
        $paginator->setDefaultItemCountPerPage(10);        
        $paginator->setCurrentPageNumber($page);
        
        // Render the view template
        return new ViewModel([
            'posts' => $paginator,
            'postManager' => $this->autoManager
        ]);        
    }
    
    
    //apagarexpirados
    /**
     * This "admin" action displays the Manage Posts page. This page contains
     * the list of posts with an ability to edit/delete any post.
     */
    public function apagarexpiradosAction()
    {
        $page = $this->params()->fromQuery('page', 1);
              
        //get de todos published
        $posts = $this->entityManager->createQuery("SELECT u FROM Empregos\Entity\Empregos u where u.status='".Empregos::STATUS_EXPIRED."' order by u.dateCreated DESC");
        $posts = $posts->getResult();
        
        
        
//                  // Find the existing post in the database.
//                $post = $this->entityManager->getRepository(Agricultura::class)
//                ->findOneById($postId);
//                
        foreach($posts as $post)  
        {
            

           $currentDate = date('Y-m-d H:i:s');   
           
           //data antiga
           $addate=$post->getDateCreated(); 
           
        
           
           $user = $this->entityManager->getRepository(User::class)
                ->findOneById($post->getIdentidade());
           
           
           if($user->getPerfil()==User::PERFIL_MEMBER)
           {
                $addateexpire = strtotime("+6 months", strtotime($addate)); // returns timestamp
                $addateexpire=date('Y-m-d H:i:s',$addateexpire);
           }else
           {
                $addateexpire = strtotime("+15 months", strtotime($addate)); // returns timestamp
                $addateexpire=date('Y-m-d H:i:s',$addateexpire);               
           }
           if($addateexpire<=$currentDate)
           
           {
               
               //apaga
               $this->autoManager->removePost($post);

           }

        }
        
        return $this->redirect()->toRoute('empregosposts', ['action'=>'adminexpired']);        
    }
    
    
    
    
    
    
     /**
     * This action displays the page allowing to edit a post.
     */
    public function editAction() 
    {
        $user = $this->entityManager->getRepository(User::class)
                        ->findOneByEmail($this->authService->getIdentity());
    
        if($user->getPerfil()==User::PERFIL_ADMIN)
        {
              $form = new AutoForm('admin');  
         }else
         {
              // Create the form.
              $form = new AutoForm();
             
         }
       
        
        // Get post ID.
        $postId = $this->params()->fromRoute('id', -1);
        
        // Validate input parameter
        if ($postId<0) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        // Find the existing post in the database.
        $post = $this->entityManager->getRepository(Empregos::class)
                ->findOneById($postId);     
        
        if ($post == null) {
            $this->getResponse()->setStatusCode(404);
            return;                        
        } 
        
        // Check whether this post is a POST request.
        if ($this->getRequest()->isPost()) {
            
            // Get POST data.
            $data = $this->params()->fromPost();
            
            // Fill form with data.
            $form->setData($data);
            if ($form->isValid()) {
                                
                // Get validated form data.
                $data = $form->getData();
                
                // Use post manager service update existing post.                
                $this->autoManager->updatePost($post, $data);
                
                
                if($user->getPerfil()==User::PERFIL_ADMIN)
                {
             
                    // Redirect the user to "admin" page.
                    return $this->redirect()->toRoute('empregosposts', ['action'=>'admin']);  
                }else
                {
             
                // Redirect the user to "admin" page.
                return $this->redirect()->toRoute('empregosposts', ['action'=>'myadmin']);
             
                }
                
            }
        } else {
            $data = [
                'title' => $post->getTitle(),
                'content' => $post->getContent(),               
                'status' => $post->getStatus(),
                'contact' => $post->getContact()
            ];
            
            $form->setData($data);
        }
        
        // Render the view template.
        return new ViewModel([
            'form' => $form,
            'post' => $post
        ]);  
    } 
     
     /**
     * This action displays the "View Post" page allowing to see the post title
     * and content. The page also contains a form allowing
     * to add a comment to post. 
     */
    public function viewAction() 
    {       
        $postId = $this->params()->fromRoute('id',-1);
        
        // Validate input parameter
        if ($postId<0) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        

        
        // Find the post by ID
        $post = $this->entityManager->getRepository(Empregos::class)
                ->findOneById($postId);
        

        
        if ($post == null) {
            $this->getResponse()->setStatusCode(404);
            return;                        
        }        
        
        if($post->getStatus()== Empregos::STATUS_APROVAR || $post->getStatus()== Empregos::STATUS_DRAFT)
        {
                 $user = $this->entityManager->getRepository(User::class)
                        ->findOneByEmail($this->authService->getIdentity());
                 
        if(isset($user)){
            
      
            
    
        if($user->getPerfil()==User::PERFIL_ADMIN || $post->getIdentidade()==$user->getId())
        {
              
         }else
         {
            $this->getResponse()->setStatusCode(404);
            return;                        
            
             
         }
            
        
          }else
        {
            $this->getResponse()->setStatusCode(404);
            return;  
            
        }
        }

        $this->autoManager->updateVisitas($post);
              
                
        // Redirect the user again to "view" page.
        //return $this->redirect()->toRoute('posts', ['action'=>'view', 'id'=>$postId]);
       
        // Render the view template.
        return new ViewModel([
            'post' => $post,
            'postManager' => $this->autoManager,
            'authService'=>$this->authService,
            'entityManager'=>$this->entityManager,
            'userManager'=>$this->userManager,
            'userImageManager'=>$this->userImageManager,   
            'empresaImageManager'=>$this->empresaImageManager,
      
        ]);
    } 
    
    
    public function contactosAction()
    {             

          // Get post ID.
        $postId = (int)$this->params()->fromRoute('id', -1);
        
        // Validate input parameter
        if ($postId<0) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        

    
     
     $mensagemdeerro=-1;

      
      
    $form = new \Auto\Form\MsgForm();

    $form->get('submit')->setAttribute('label', 'Reply');
    
    
    $request = $this->getRequest();
    if ($request->isPost()) 
    {
    
            // Fill in the form with POST data
            $ad = $this->params()->fromPost(); 
       
        $form->setData($ad);
        
   if ($form->isValid()) 
       {

         $mess=$form->getData();
         
         //gravar mensagem na base de dados
         // Use post manager service to add new post to database.                
         $this->autoManager->addNewMsg($mess,$postId);
         
         
         $re=$this->sendNotificationReplyEmail($postId,$mess['email'],$mess['name'],$mess['mensagem']);
      
         //envia email 
         if($re==1)
         {
             $mensagemdeerro=1;
         }elseif($re==0)
         {
             $mensagemdeerro=0;
             
         }elseif($re==-2)
         {
             $mensagemdeerro=-2;
         }
      
 
    }
        
        
      
    }
 
    return array(
        'mensagemerro'=> $mensagemdeerro,
        'form' => $form,
    );

    
        
   
    }
    
   
    
    
    
        public function sendNotificationReplyEmail($postId,$email,$name,$mensagem)
{
            
                //temos o postId sacar a identidade
                
            
                $httpHost = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'localhost';
                $adURL = 'http://' . $httpHost . '/empregosposts/view/' . $postId;
                
                $post = $this->entityManager->getRepository(Empregos::class)
                ->findOneById($postId);
                
                $user = $this->entityManager->getRepository(User::class)
                ->findOneById($post->getIdentidade());
                
                $u=$user->getUnsubscribe();
                
                if($u==0)
                {
                
                $sendemail=$user->getEmail();
                $useridforunsubscribe=$user->getId();
                $adURLuseridforunsubscribe = 'http://' . $httpHost . '/unsubscribe/unsubscribe/' . $useridforunsubscribe;
            
		$transport = $this->mailtransport; //$this->getServiceLocator()->get('mail.transport');
		$message = new Message();
		$this->getRequest()->getServer();  //Server vars
                $htmlMarkup='The ad :<br>'.$adURL.'<br><br>This email was sent for your email : <br> '.$sendemail.' <br><br> The link to unsubscribe : <br>'.$adURLuseridforunsubscribe.'<br><br><br>';
                $html = new MimePart($htmlMarkup);
                $html->type = "text/html";
                $body = new MimeMessage();
                $body->setParts(array($html));
		$message->addTo($sendemail)
				->addFrom('no_reply@thejoboard.com')
				->setSubject('Someone replied to your ad at www.thejoboard.com')
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
                
                }else
                {
                    return -2;
                    
                }
                
		
}
    

     /**
     * This action displays the "View Post" page allowing to see the post title
     * and content. The page also contains a form allowing
     * to add a comment to post. 
     */
    public function seemessagesAction() 
    {   
        
        $page = $this->params()->fromQuery('page', 1);
        $postId = (int)$this->params()->fromRoute('id', -1);
        
        // Validate input parameter
        if ($postId<0) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        $user = $this->entityManager->getRepository(\User\Entity\User::class)
                        ->findOneByEmail($this->authService->getIdentity());
    
        if(isset($user))
        {
            $identidade=$user->getId();
            $perfil=$user->getPerfil();
        }else
        {
            $identidade=null;
             $perfil=null;
        }
        
              // Find the post by ID
        $post = $this->entityManager->getRepository(Empregos::class)
                ->findOneById($postId);
        
        if ($post == null) {
            $this->getResponse()->setStatusCode(404);
            return;                        
        }   
        
        if(($identidade!=$post->getIdentidade()) && ($perfil!=\User\Entity\User::PERFIL_ADMIN))
        {
            $this->getResponse()->setStatusCode(404);
            return;     
        }

                
        $msg = $this->entityManager->createQuery("SELECT u FROM Empregos\Entity\Msgempregos u where u.iddoanuncioauto='".$postId."' order by u.id DESC");
        
        
        
        if ($msg == null) {
            $this->getResponse()->setStatusCode(404);
            return;                        
        }        
        
    
        $adapter = new DoctrineAdapter(new ORMPaginator($msg, false));
        $paginator = new Paginator($adapter);
        $paginator->setDefaultItemCountPerPage(10);        
        $paginator->setCurrentPageNumber($page);
        
        
        
        // Render the view template.
        return new ViewModel([
            'posts' => $paginator,
            'iddoanuncio'=>$postId,           
        ]);
    } 

  /**
     * This action displays the "View Post" page allowing to see the post title
     * and content. The page also contains a form allowing
     * to add a comment to post. 
     */
    public function viewmessageAction() 
    {       
        $postId = (int)$this->params()->fromRoute('id', -1);
        $anuncioId = (int)$this->params()->fromRoute('idanuncio', -1);
        
        
        // Validate input parameter
        if (($postId<0)||($anuncioId<0)) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        
        
        $user = $this->entityManager->getRepository(\User\Entity\User::class)
                        ->findOneByEmail($this->authService->getIdentity());
        
        if ($user == null) {
            $this->getResponse()->setStatusCode(404);
            return;                        
        } 
    
        if(isset($user))
        {
            $identidade=$user->getId();
            $perfil=$user->getPerfil();
        }else
        {
            $identidade=null;
             $perfil=null;
        }
        
  
        
        // Find the post by ID
        $post = $this->entityManager->getRepository(Empregos::class)
                ->findOneById($anuncioId);
        
        
        if ($post == null) {
            $this->getResponse()->setStatusCode(404);
            return;                        
        }     
        
        if(($identidade!=$post->getIdentidade()) && ($perfil!=\User\Entity\User::PERFIL_ADMIN))
        {
            $this->getResponse()->setStatusCode(404);
            return;     
        }

        
                
        //$msg = $this->entityManager->createQuery("SELECT u FROM Motas\Entity\Msgmotas u where u.iddoanuncioauto='".$postId."' order by u.id DESC");
        
           // Find the post by ID
        $msg = $this->entityManager->getRepository(Msgempregos::class)
                ->findOneById($postId);
        
        
        if ($msg == null) {
            $this->getResponse()->setStatusCode(404);
            return;                        
        }        
        
    
          
       
        // Render the view template.
        return new ViewModel([
            'post' => $msg, 
           
        
        ]);
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
        
        $post = $this->entityManager->getRepository(Empregos::class)
                ->findOneById($postId);        
        if ($post == null) {
            $this->getResponse()->setStatusCode(404);
            return;                        
        }        
        
        $this->autoManager->removePost($post);
        $user = $this->entityManager->getRepository(User::class)
                        ->findOneByEmail($this->authService->getIdentity());
        if($user->getPerfil()==User::PERFIL_ADMIN)
        {

            // Redirect the user to "admin" page.
            return $this->redirect()->toRoute('empregosposts', ['action'=>'admin']);  
        }else
        {

        // Redirect the user to "admin" page.
        return $this->redirect()->toRoute('empregosposts', ['action'=>'myadmin']);

        }      
                
    }
    
    
    
    
    
            /**
     * This "delete" action deletes the given post.
     */
    public function deleteMessageAction()
    {
        
        $postId = (int)$this->params()->fromRoute('idanuncio', -1);
        $msgId = (int)$this->params()->fromRoute('id', -1);
        
        // Validate input parameter
        if (($msgId<0) || ($postId<0)) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        $post = $this->entityManager->getRepository(Msgempregos::class)
                ->findOneById($msgId); 
        
        
         if ($post == null) {
            $this->getResponse()->setStatusCode(404);
            return;                        
        }        
        
        $this->autoManager->removeMessagePost($post);
        
        
      

            // Redirect the user to "admin" page.
            return $this->redirect()->toRoute('empregosposts', ['action'=>'seemessages','id'=>$postId]);  
          
                
    }

}