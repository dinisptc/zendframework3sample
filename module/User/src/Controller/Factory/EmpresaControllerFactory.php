<?php
namespace User\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use User\Controller\EmpresaController;
use User\Service\EmpresaManager;
use User\Service\EmpresaImageManager;

/**
 * This is the factory for UserController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class EmpresaControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $userManager = $container->get(EmpresaManager::class);
        $userImageManager = $container->get(EmpresaImageManager::class);
        
        $mailtransport = $container->get('mail.transport');
           $translator = $container->get('translator');
        // Instantiate the controller and inject dependencies
        return new EmpresaController($entityManager, $userManager, $mailtransport,$translator,$userImageManager);
    }
}