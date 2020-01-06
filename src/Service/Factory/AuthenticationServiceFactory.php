<?php
namespace Mf\Users\Service\Factory;

use Interop\Container\ContainerInterface;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Session\SessionManager;
use Laminas\Authentication\Storage\Session as SessionStorage;
use Mf\Users\Service\AuthAdapter;


/**
 * The factory responsible for creating of authentication service.
 */
class AuthenticationServiceFactory implements FactoryInterface
{
    /**
    *
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $sessionManager = $container->get(SessionManager::class);
        $authStorage = new SessionStorage('Simba_Auth', 'session', $sessionManager);
        $authAdapter = $container->get(AuthAdapter::class);

        return new $requestedName($authStorage, $authAdapter);
    }
}

