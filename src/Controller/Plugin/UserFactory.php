<?php
/**
* фабрика для плагина контроллеров User, 
* 
*/

namespace Mf\Users\Controller\Plugin;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
//use Laminas\Authentication\AuthenticationService;

use Mf\Users\Service\User;

class UserFactory implements FactoryInterface
{
    /**
     * 
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {        

        return new $requestedName($container->get(User::class));

    }
    /**
     * Create and return Acl instance
     *
     * For use with Laminas-servicemanager v2; proxies to __invoke().
     *
     * @param ServiceLocatorInterface $container
     * @return Acl
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, User::class);
    }

}
