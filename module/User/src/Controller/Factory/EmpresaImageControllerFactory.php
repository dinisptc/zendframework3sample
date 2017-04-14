<?php
namespace User\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use User\Service\EmpresaimageManager;
use User\Controller\EmpresaImageController;
use User\Service\EmpresaManager;
/**
 * This is the factory for ImageController. Its purpose is to instantiate the
 * controller.
 */
class EmpresaImageControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $imageManager = $container->get(EmpresaimageManager::class);
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $postManager = $container->get(EmpresaManager::class);
        // Instantiate the controller and inject dependencies
        return new EmpresaImageController($imageManager, $entityManager, $postManager);
    }
}

