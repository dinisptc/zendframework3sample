<?php
namespace User\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use User\Service\EmpresaImageManager;
use User\Controller\LogoController;
use User\Service\EmpresaManager;
/**
 * This is the factory for ImageController. Its purpose is to instantiate the
 * controller.
 */
class LogoControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $imageManager = $container->get(EmpresaImageManager::class);
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $postManager = $container->get(EmpresaManager::class);
        // Instantiate the controller and inject dependencies
        return new LogoController($imageManager, $entityManager, $postManager);
    }
}

