<?php
namespace Mf\Users\Service\Factory;

use Interop\Container\ContainerInterface;

use Mf\Users\Service\UserManager;
use Mf\Users\Service\User;
use Laminas\Authentication\AuthenticationService;

/**
 */
class UserFactory
{
    /**
     * This method creates the UserManager service and returns its instance. 
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {        
        $helper = new User();
        if ($container->has(AuthenticationService::class)) {
            $helper->setAuthenticationService($container->get(AuthenticationService::class));
        }
        $helper->setUserManager($container->get(UserManager::class));
        return $helper;
    }
}
