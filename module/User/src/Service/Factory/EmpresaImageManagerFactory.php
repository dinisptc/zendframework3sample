<?php
namespace User\Service\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use User\Service\EmpresaImageManager;
use User\Controller\ImageController;
use User\Service\EmpresaManager;
/**
 * This is the factory for ImageController. Its purpose is to instantiate the
 * controller.
 */
class EmpresaImageManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
   
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
  
        // Instantiate the controller and inject dependencies
        return new EmpresaImageManager($entityManager);
    }
}
