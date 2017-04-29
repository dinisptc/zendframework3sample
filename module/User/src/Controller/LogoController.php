<?php
namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use User\Form\LogoForm;
use User\Entity\Empresas;
/**
 * This controller is designed for managing image file uploads.
 */
class LogoController extends AbstractActionController 
{
    /**
     * Image manager.
     * @var Application\Service\ImageManager;
     */
    private $imageManager;
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
     * Constructor.
     */
    public function __construct($imageManager,$entityManager,$postManager)
    {
        $this->imageManager = $imageManager;
        $this->entityManager = $entityManager;
        $this->autoManager = $postManager;
    }
    
    /**
     * This is the default "index" action of the controller. It displays the 
     * Image Gallery page which contains the list of uploaded images.
     */
    public function indexAction() 
    {
        
        $postId = $this->params()->fromRoute('id', -1);
        
        // Validate input parameter
        if ($postId<0) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        
        // Get the list of already saved files.
        $files = $this->imageManager->getSavedFiles($postId);
        
        
        
        // Render the view template
        return new ViewModel([
            'files'=>$files,
            'postId'=>$postId,
        ]);
    }
    
    /**
     * This action shows the image upload form. This page allows to upload 
     * a single file.
     */
    public function uploadAction() 
    {
        
                
        $postId = $this->params()->fromRoute('id', -1);
        
        // Validate input parameter
        if ($postId<0) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        
        
                // Create the form model
        $form = new LogoForm($postId);
        
        $files = $this->imageManager->getSavedFiles($postId);
        
        
        $conta=0;
        foreach($files as $file)
        {
            $conta++;
        }
        if($conta>0){ 
            
        $mensagem=_('Only can upload one personal picture');
                    // Render the page
        return new ViewModel([
            'form' => $form,
            'message'=>$mensagem,
        ]);
            
        }

        
        // Check if user has submitted the form
        if($this->getRequest()->isPost()) {
            
            // Make certain to merge the files info!
            $request = $this->getRequest();
            $data = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            
            // Pass data to form
            $form->setData($data);
            
            // Validate form
            if($form->isValid()) {
                
                // Move uploaded file to its destination directory.
                $data = $form->getData();
                
                          
                // Find the existing post in the database.
//                $post = $this->entityManager->getRepository(User::class)
//                ->findOneById($postId);
//                
//                
//                
//                //fazer update com aprovacao
//                  // Use post manager service update existing post.                
//                $this->autoManager->updateAprovarPost($post);
                
                // Redirect the user to "Image Gallery" page
                return $this->redirect()->toRoute('empresaimages', ['action'=>'index', 'id'=>$postId]);
            }                        
        } 
        
        // Render the page
        return new ViewModel([
            'form' => $form,
            'message'=>'',
        ]);
    }
    
    /**
     * This is the 'file' action that is invoked when a user wants to 
     * open the image file in a web browser or generate a thumbnail.        
     */
    public function fileAction() 
    {
        
        $postId = $this->params()->fromRoute('id', -1);
        
        // Validate input parameter
        if ($postId<0) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        // Get the file name from GET variable
        $fileName = $this->params()->fromQuery('name', '');
                
        // Check whether the user needs a thumbnail or a full-size image
        $isThumbnail = (bool)$this->params()->fromQuery('thumbnail', false);
        
        // Validate input parameters
        if (empty($fileName) || strlen($fileName)>128) {
            throw new \Exception('File name is empty or too long');
        }
        
        // Get path to image file
        $fileName = $this->imageManager->getImagePathByName($fileName,$postId);
                
        if($isThumbnail) {        
            // Resize the image
            $fileName = $this->imageManager->resizeImage($fileName);
        }else{
            
           // $fileName = $this->imageManager->watermainImage($fileName);                        
        }
        
        
        
                
        // Get image file info (size and MIME type).
        $fileInfo = $this->imageManager->getImageFileInfo($fileName);        
        if ($fileInfo===false) {
            // Set 404 Not Found status code
            $this->getResponse()->setStatusCode(404);            
            return;
        }
                
        // Write HTTP headers.
        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeaderLine("Content-type: " . $fileInfo['type']);        
        $headers->addHeaderLine("Content-length: " . $fileInfo['size']);
            
        // Write file content        
        $fileContent = $this->imageManager->getImageFileContent($fileName);
        if($fileContent!==false) {                
            $response->setContent($fileContent);
        } else {        
            // Set 500 Server Error status code
            $this->getResponse()->setStatusCode(500);
            return;
        }
        
        if($isThumbnail) {
            // Remove temporary thumbnail image file.
            unlink($fileName);
        }
        
        // Return Response to avoid default view rendering.
        return $this->getResponse();
    }    
    
    
    
    
     /**
     * This is the 'file' action that is invoked when a user wants to 
     * open the image file in a web browser or generate a thumbnail.        
     */
    public function deletefileAction() 
    {
        
        $postId = $this->params()->fromRoute('id', -1);
        
        // Validate input parameter
        if ($postId<0) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        // Get the file name from GET variable
        $fileName = $this->params()->fromQuery('name', '');
                
        // Check whether the user needs a thumbnail or a full-size image
        $isThumbnail = (bool)$this->params()->fromQuery('thumbnail', false);
        
        // Validate input parameters
        if (empty($fileName) || strlen($fileName)>128) {
            throw new \Exception('File name is empty or too long');
        }
        
        // Get path to image file
        $fileName = $this->imageManager->getImagePathByName($fileName,$postId);
        
        
      // unlink($fileName);
        
           // Get the list of already saved files.
        $files = $this->imageManager->getSavedFiles($postId);
        
        
        if (count($files)>0)
        {
           unlink($fileName);
        }
        
        $files = $this->imageManager->getSavedFiles($postId);
        
        
               // Render the page
        return new ViewModel([
            'files'=>$files,
            'postId' => $postId
        ]);
    }    
}


