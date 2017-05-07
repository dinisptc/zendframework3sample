<?php
namespace Curriculos\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Curriculos\Service\PdfManager;
use Curriculos\Controller\PdfController;
use Curriculos\Service\AutoManager;
/**
 * This is the factory for ImageController. Its purpose is to instantiate the
 * controller.
 */
class PdfControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $imageManager = $container->get(PdfManager::class);
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $postManager = $container->get(AutoManager::class);
        // Instantiate the controller and inject dependencies
        return new PdfController($imageManager, $entityManager, $postManager);
    }
}

