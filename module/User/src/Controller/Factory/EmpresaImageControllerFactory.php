<?php
namespace User\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use User\Service\ImageManager;
use User\Controller\ImageController;
use User\Service\UserManager;
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
        $postManager = $container->get(UserManager::class);
        // Instantiate the controller and inject dependencies
        return new ImageController($imageManager, $entityManager, $postManager);
    }
}

