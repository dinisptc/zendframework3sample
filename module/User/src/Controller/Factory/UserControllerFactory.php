<?php
namespace User\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use User\Controller\UserController;
use User\Service\UserManager;
use User\Service\ImageManager;
use User\Service\EmpresaManager;

/**
 * This is the factory for UserController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class UserControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $userManager = $container->get(UserManager::class);
        $userImageManager = $container->get(ImageManager::class);
        
        $mailtransport = $container->get('mail.transport');
        $translator = $container->get('translator');
           
        $empresaManager = $container->get(EmpresaManager::class);
        
        
        // Instantiate the controller and inject dependencies
        return new UserController($entityManager, $userManager, $mailtransport,$translator,$userImageManager,$empresaManager);
    }
}