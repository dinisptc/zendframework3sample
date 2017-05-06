<?php
namespace Curriculos\Service;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Curriculos\Entity\Empregos;

use Zend\Filter\StaticFilter;
use User\Entity\User;

/**
 * The PostManager service is responsible for adding new posts, updating existing
 * posts, adding tags to post, etc.
 */
class AutoManager
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager;
     */
    private $entityManager;
    
        /**
     * Authentication service.
     * @var \Zend\Authentication\AuthenticationService
     */
    private $authService;
    
    /**
     * Constructor.
     */
    public function __construct($entityManager,$authService)
    {
        $this->entityManager = $entityManager;
        $this->authService = $authService;
    }
    
    /**
     * This method adds a new post.
     */
    public function addNewPost($data) 
    {
        // Create new Post entity.
        $post = new Empregos();
        
        $post->setId(uniqid('job_'));
        $post->setTitle($data['title']);
        $post->setContent($data['content']);
       
        $currentDate = date('Y-m-d H:i:s');                
        $post->setDateCreated($currentDate); 
        
        $user = $this->entityManager->getRepository(User::class)
                        ->findOneByEmail($this->authService->getIdentity());
        $ident=$user->getId();                 
        $post->setIdentidade($user->getId()); 
        if($user->getPerfil()==User::PERFIL_ADMIN)
        {
             
             $post->setStatus($data['status']);  
         }else
         {
            //$post->setStatus(Auto::STATUS_APROVAR);
             
             if($data['status']==Empregos::STATUS_DRAFT)
             {
                $post->setStatus(Empregos::STATUS_DRAFT);
                 
             }else
             {
                $post->setStatus(Empregos::STATUS_APROVAR);
             }
             
         }
         $post->setContact($data['contact']);  
         $post->setNumvisits(1);
        
        // Add the entity to entity manager.
        $this->entityManager->persist($post);
        
  
        
        // Apply changes to database.
        $this->entityManager->flush();
    }
    
    /**
     * This method allows to update data of a single post.
     */
    public function updatePost($post, $data) 
    {
        $post->setTitle($data['title']);
        $post->setContent($data['content']);
        $post->setContact($data['contact']);
        
        
        $user = $this->entityManager->getRepository(User::class)
                        ->findOneByEmail($this->authService->getIdentity());
                                   
        
        if($user->getPerfil()==User::PERFIL_ADMIN)
        {
             
             $post->setStatus($data['status']);  
              if($data['status']==Empregos::STATUS_PUBLISHED)
             {                             
                $currentDate = date('Y-m-d H:i:s');                
                $post->setDateCreated($currentDate);
             } 
             
             if($data['status']==Empregos::STATUS_APROVAR)
             {                             
                $currentDate = date('Y-m-d H:i:s');                
                $post->setDateCreated($currentDate);
             }
         }else
         {
             if($data['status']==Empregos::STATUS_DRAFT)
             {
                $post->setStatus(Empregos::STATUS_DRAFT);
                 
             }elseif($data['status']==Empregos::STATUS_EXPIRED)
             {
                $post->setStatus(Empregos::STATUS_EXPIRED);
                 
             }else{
                $post->setStatus(Empregos::STATUS_APROVAR);
                $currentDate = date('Y-m-d H:i:s');                
                $post->setDateCreated($currentDate);
             }
             
         }
    
        
        // Apply changes to database.
        $this->entityManager->flush();
    }

    /**
     * Adds/updates tags in the given post.
     */
    private function addTagsToPost($tagsStr, $post) 
    {
        // Remove tag associations (if any)
        $tags = $post->getTags();
        foreach ($tags as $tag) {            
            $post->removeTagAssociation($tag);
        }
        
        // Add tags to post
        $tags = explode(',', $tagsStr);
        foreach ($tags as $tagName) {
            
            $tagName = StaticFilter::execute($tagName, 'StringTrim');
            if (empty($tagName)) {
                continue; 
            }
            
            $tag = $this->entityManager->getRepository(Tag::class)
                    ->findOneByName($tagName);
            if ($tag == null)
                $tag = new Tag();
            
            $tag->setName($tagName);
            $tag->addPost($post);
            
            $this->entityManager->persist($tag);
            
            $post->addTag($tag);
        }
    }    
    
    /**
     * Returns status as a string.
     */
    public function getPostStatusAsString($post) 
    {
        switch ($post->getStatus()) {
            case Empregos::STATUS_DRAFT: return _('Draft');
            case Empregos::STATUS_PUBLISHED: return _('Published');
            case Empregos::STATUS_APROVAR: return _('Pending Approval');
            case Empregos::STATUS_EXPIRED: return _('Expired');
        }
        
        return 'Unknown';
    }
    
    /**
     * Converts tags of the given post to comma separated list (string).
     */
    public function convertTagsToString($post) 
    {
        $tags = $post->getTags();
        $tagCount = count($tags);
        $tagsStr = '';
        $i = 0;
        foreach ($tags as $tag) {
            $i ++;
            $tagsStr .= $tag->getName();
            if ($i < $tagCount) 
                $tagsStr .= ', ';
        }
        
        return $tagsStr;
    }    

    /**
     * Returns count of comments for given post as properly formatted string.
     */
    public function getCommentCountStr($post)
    {
        $commentCount = count($post->getComments());
        if ($commentCount == 0)
            return 0;
        else if ($commentCount == 1) 
            return 1;
        else
            return $commentCount;
    }


    /**
     * This method adds a new comment to post.
     */
    public function addCommentToPost($post, $data) 
    {
        // Create new Comment entity.
        $comment = new Comment();
        $comment->setPost($post);
        $comment->setAuthor($data['author']);
        $comment->setContent($data['comment']);        
        $currentDate = date('Y-m-d H:i:s');
        $comment->setDateCreated($currentDate);

        // Add the entity to entity manager.
        $this->entityManager->persist($comment);

        // Apply changes.
        $this->entityManager->flush();
    }
    
    /**
     * Removes post and all associated comments.
     */
    public function removePost($post) 
    {

        
        $postid=$post->getId();
        
        $this->rrmdir('./public/files/empregos/'.$postid.'/');
        
        //apagar as mensagens
        
//        $messages = $this->entityManager->createQuery("SELECT u FROM Empregos\Entity\Msgempregos u where u.iddoanuncioauto='".$postid."'");
//        $messages=$messages->getResult();
//        
//        foreach ($messages as $message) {
//            $this->entityManager->remove($message);
//        }
        
        $this->entityManager->remove($post);
        
        $this->entityManager->flush();
    }
    
        /**
     * Removes post and all associated comments.
     */
    public function removeMessagePost($post) 
    {
                      
        $this->entityManager->remove($post);
      
        
        $this->entityManager->flush();
    }
    
    
    /**
     * Calculates frequencies of tag usage.
     */
    public function getTagCloud()
    {
        $tagCloud = [];
                
        $posts = $this->entityManager->getRepository(Post::class)
                    ->findPostsHavingAnyTag();
        $totalPostCount = count($posts);
        
        $tags = $this->entityManager->getRepository(Tag::class)
                ->findAll();
        
        
        foreach ($tags as $tag) {
            
            $postsByTag = $this->entityManager->getRepository(Post::class)
                    ->findPostsByTag($tag->getName());
            
            $postCount = count($postsByTag);
            if ($postCount > 0) {
                $tagCloud[$tag->getName()] = $postCount;
            }
        }
        
        $normalizedTagCloud = [];
        
        // Normalize
        foreach ($tagCloud as $name=>$postCount) {
            $normalizedTagCloud[$name] =  $postCount/$totalPostCount;
        }
        
        return $normalizedTagCloud;
    }
    
    
    
     /**
     * Removes post and all associated comments.
     */
    public function removeComment($comment) 
    {
        $this->entityManager->remove($comment);
        
        $this->entityManager->flush();
    }
    
    
        /**
     * This method adds a new post.
     */
    public function addNewMsg($data,$postId) 
    {
        // Create new Post entity.
        $post = new Msgempregos();
        
        
        $post->setEmail($data['email']);
        $post->setMensagem($data['mensagem']);
        $post->setName($data['name']);
        
        $post->setIddoanuncioauto($postId);
        
        
        
        
        // Add the entity to entity manager.
        $this->entityManager->persist($post);
        
  
        
        // Apply changes to database.
        $this->entityManager->flush();
    }
    
          /**
     * This method allows to update data of a single post.
     */
    public function updateVisitas($post) 
    {
       
        
        
        $numvisitas=$post->getNumvisits();
        $numvisitas=$numvisitas+1;
  
        $post->setNumvisits($numvisitas);
         
    
        
        // Apply changes to database.
        $this->entityManager->flush();
    }  
   public function rrmdir($dir) {
   if (is_dir($dir)) {
     $objects = scandir($dir);
     foreach ($objects as $object) {
       if ($object != "." && $object != "..") {
         if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
       }
     }
     reset($objects);
     rmdir($dir);
   }
}

    /**
     * This method allows to update data of a single post.
     */
    public function updateAprovarPost($post) 
    {

        $post->setStatus(Empregos::STATUS_APROVAR);

        // Apply changes to database.
        $this->entityManager->flush();
    }
    
             /**
     * This method allows to update data of a single post.
     */
    public function expirepost($post) 
    {

        $post->setStatus(Empregos::STATUS_EXPIRED);

        // Apply changes to database.
        $this->entityManager->flush();
    }

}
