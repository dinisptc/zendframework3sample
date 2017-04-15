<?php
namespace User\Service\Factory;

use Interop\Container\ContainerInterface;
use User\Service\EmpresaManager;

/**
 * This is the factory class for UserManager service. The purpose of the factory
 * is to instantiate the service and pass it dependencies (inject dependencies).
 */
class EmpresaManagerFactory
{
    /**
     * This method creates the UserManager service and returns its instance. 
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {        
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        
        $mailtransport = $container->get('mail.transport');
                        
        return new EmpresaManager($entityManager,$mailtransport);
    }
}
