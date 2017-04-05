<?php
namespace Empregos\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Empregos\Service\AutoManager;
use Empregos\Controller\EmpregosController;
use Empregos\Service\ImageManager;

/**
 * This is the factory for PostController. Its purpose is to instantiate the
 * controller.
 */
class EmpregosControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $postManager = $container->get(AutoManager::class);
            // Instantiate dependencies.
        $authenticationService = $container->get(\Zend\Authentication\AuthenticationService::class);
        
        $translator = $container->get('translator');
            
        $mailtransport = $container->get('mail.transport');
               
        $imageManager = $container->get(ImageManager::class);
        
        // Instantiate the controller and inject dependencies
        return new EmpregosController($entityManager, $postManager, $authenticationService, $mailtransport, $translator, $imageManager);
    }
}