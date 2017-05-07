<?php
namespace Curriculos\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Curriculos\Service\AutoManager;
use Curriculos\Controller\CurriculosController;
use Curriculos\Service\PdfManager;


use User\Service\UserManager;
use User\Service\ImageManager;
use User\Service\EmpresaManager;
use User\Service\EmpresaImageManager;



/**
 * This is the factory for PostController. Its purpose is to instantiate the
 * controller.
 */
class CurriculosControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $postManager = $container->get(AutoManager::class);
            // Instantiate dependencies.
        $authenticationService = $container->get(\Zend\Authentication\AuthenticationService::class);
        
        $translator = $container->get('translator');
            
        $mailtransport = $container->get('mail.transport');
               
  
        
        $userManager = $container->get(UserManager::class);
        $userImageManager = $container->get(ImageManager::class);
        
        $empresaImageManager = $container->get(EmpresaImageManager::class);
        
        // Instantiate the controller and inject dependencies
        return new CurriculosController($entityManager, $postManager, $authenticationService, $mailtransport, $translator, $userManager, $userImageManager,$empresaImageManager);
    }
}