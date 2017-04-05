<?php
namespace Empregos\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Empregos\Service\ImageManager;
use Empregos\Controller\ImageController;
use Empregos\Service\AutoManager;
/**
 * This is the factory for ImageController. Its purpose is to instantiate the
 * controller.
 */
class ImageControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $imageManager = $container->get(ImageManager::class);
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $postManager = $container->get(AutoManager::class);
        // Instantiate the controller and inject dependencies
        return new ImageController($imageManager, $entityManager, $postManager);
    }
}

