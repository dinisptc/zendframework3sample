<?php
namespace Empregos\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Empregos\Service\MailSender;
use Empregos\Entity\Empregos;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;
use Empregos\Form\SearchForm;

/**
 * This is the main controller class of the Blog application. The 
 * controller class is used to receive user input,  
 * pass the data to the models and pass the results returned by models to the 
 * view for rendering.
 */
class IndexController extends AbstractActionController 
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
    
     /**
     * Image manager.
     * @var Application\Service\ImageManager;
     */
    private $imageManager;
    
    /**
     * Constructor is used for injecting dependencies into the controller.
     */
    public function __construct($entityManager, $postManager, $authService, $imageManager) 
    {
        $this->entityManager = $entityManager;
        $this->autoManager = $postManager;
        $this->authService = $authService;
        $this->imageManager = $imageManager;
            
    }
    

        /**
     * This is the default "index" action of the controller. It displays the 
     * Recent Posts page containing the recent blog posts.
     */
    public function indexAction() 
    {
        $page = $this->params()->fromQuery('page', 1);
       
           
        $posts = $this->entityManager->createQuery("SELECT u FROM Empregos\Entity\Empregos u where u.status=2 order by u.dateCreated DESC");
            

        
        $adapter = new DoctrineAdapter(new ORMPaginator($posts, false));
        $paginator = new Paginator($adapter);
        $paginator->setDefaultItemCountPerPage(10);        
        $paginator->setCurrentPageNumber($page);
        
        
        
        $contaexpirados=0;
        $contaparamember=0;
        
        $user = $this->entityManager->getRepository(\User\Entity\User::class)
                        ->findOneByEmail($this->authService->getIdentity());
    
        if(isset($user))
        {
            $identidade=$user->getId();
            $perfil=$user->getPerfil();
            $dql = "SELECT COUNT(p) FROM Empregos\Entity\Empregos p where p.status='".Empregos::STATUS_EXPIRED."' and p.identidade='".$identidade."'";
            $q = $this->entityManager->createQuery($dql);
            $contaexpirados = $q->getSingleScalarResult();
            
            
            $dqlmemb = "SELECT COUNT(p) FROM Empregos\Entity\Empregos p where p.status='".Empregos::STATUS_PUBLISHED."' and p.identidade='".$identidade."'";
            $q1 = $this->entityManager->createQuery($dqlmemb);
            $contaparamember = $q1->getSingleScalarResult();
            
        }else
        {
            $identidade=null;
            $perfil=null;
        }
        
    
            
       
        
        // Render the view template.
        return new ViewModel([
            'posts' => $paginator,
            'postManager' => $this->autoManager,
            'identidadedesteuser'=>$identidade,
            'imageManager'=>$this->imageManager,
            'entityManager'=>$this->entityManager, 
            'perfil'=>$perfil,
            'expirados'=>$contaexpirados,
         
        ]);
    }
    
         /**
     * This is the default "index" action of the controller. It displays the 
     * Recent Posts page containing the recent blog posts.
     */
    public function pesquisaAction() 
    {
        
         $search_by_in='';
        $request = $this->getRequest();

        if ($request->isPost()) {
           
            $formdata    = (array) $request->getPost();
            $search_data = array();
            foreach ($formdata as $key => $value) {
                if ($key != 'submit') {
                    if (!empty($value)) {
                        $search_data[$key] = $value;
                    }
                }
            }
            if (!empty($search_data)) {
                $search_by_in= json_encode($search_data);
           
            }
        }

        $searchform = new SearchForm();
        $searchform->get('submit')->setValue('Search');

        $page = $this->params()->fromQuery('page') ? (int) $this->params()->fromQuery('page') : 1;

       $search_by = $this->params()->fromQuery('search_by') ? $this->params()->fromQuery('search_by') : $search_by_in;
    
       
        $posts=null;
        $entityManager = $this->entityManager;
        $formdata = array();
         if (!empty($search_by)) {
             $formdata = (array) json_decode($search_by);
             
             if(empty($formdata['search'])) { $formdata['search']=''; }
  
                     
                $posts = $entityManager->createQuery("SELECT u FROM Empregos\Entity\Empregos u WHERE "                                   
                . "(u.title LIKE '%".$formdata['search']."%'"
                . "OR u.content LIKE '%".$formdata['search']."%')"
                . "AND u.status='".Empregos::STATUS_PUBLISHED."'"
                . "order by u.dateCreated DESC");//->setHydrationMode(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY); 
       
         }
         if($posts!=null)
         {

  
        $adapter = new DoctrineAdapter(new ORMPaginator($posts, false));
        $paginator = new Paginator($adapter);
                     $paginator->setDefaultItemCountPerPage(10);        
        $paginator->setCurrentPageNumber($page);
             }else
         {
             $paginator=null;
             
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

        
        // Render the view template.
        return new ViewModel([
            'posts' => $paginator,
            'postManager' => $this->autoManager,
            'identidadedesteuser'=>$identidade,
            'imageManager'=>$this->imageManager,
            'form' => $searchform,
            'search_by'  => $search_by,
            'entityManager'=>$this->entityManager,
            'perfil'=>$perfil,
        ]);
    }   
    
    
  
}