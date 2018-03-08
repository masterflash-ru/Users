<?php
namespace Mf\Users\Controller\Factory;

use Interop\Container\ContainerInterface;

use Zend\ServiceManager\Factory\FactoryInterface;
use Mf\Permissions\Service\AuthManager;

/**
 * This is the factory for AuthController. Its purpose is to instantiate the controller
 * and inject dependencies into its constructor.
 */
class AuthControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {   

        $authManager = $container->get(AuthManager::class);
    
        return new $requestedName($authManager);
    }
}
