<?php
namespace Empregos\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
//use Empregos\Service\ImageManager;
use User\Service\EmpresaImageManager;
use Empregos\Service\AutoManager;
use Empregos\Controller\IndexController;

/**
 * This is the factory for IndexController. Its purpose is to instantiate the
 * controller.
 */
class IndexControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $postManager = $container->get(AutoManager::class);
        // Instantiate dependencies.
        $authenticationService = $container->get(\Zend\Authentication\AuthenticationService::class);
        
        $imageManager = $container->get(EmpresaImageManager::class);
        
        // Instantiate the controller and inject dependencies
        return new IndexController($entityManager, $postManager, $authenticationService, $imageManager);
    }
}




