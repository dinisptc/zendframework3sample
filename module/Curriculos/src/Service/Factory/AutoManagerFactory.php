<?php
namespace Empregos\Service\Factory;


use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Empregos\Service\AutoManager;

/**
 * This is the factory for PostManager. Its purpose is to instantiate the
 * service.
 */
class AutoManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
           // Instantiate dependencies.
        $authenticationService = $container->get(\Zend\Authentication\AuthenticationService::class);
        
        // Instantiate the service and inject dependencies
        return new AutoManager($entityManager,$authenticationService);
    }
}

