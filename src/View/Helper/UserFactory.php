<?php
/**
* фабрика для плагина контроллеров User, 
* 
*/

namespace Mf\Users\View\Helper;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Mf\Users\Service\User;

class UserFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new $requestedName($container->get(User::class));
    }
}
