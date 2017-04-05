<?php
namespace Application\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Application\Controller\IndexController;

use User\Service\UserManager;
use User\Service\ImageManager;

/**
 * This is the factory for IndexController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class IndexControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        
        $translator = $container->get('translator');
        
        $mailtransport = $container->get('mail.transport');
        
        $userManager = $container->get(UserManager::class);
             
        $userImageManager = $container->get(ImageManager::class);
        
        
            // Instantiate dependencies.
        $authenticationService = $container->get(\Zend\Authentication\AuthenticationService::class);
        
        // Instantiate the controller and inject dependencies
        return new IndexController($entityManager, $translator,$mailtransport, $userManager, $authenticationService, $userImageManager);
    }
}