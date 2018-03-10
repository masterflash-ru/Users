<?php
namespace Mf\Users\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Mf\Permissions\Service\UserManager;

use Zend\Validator\Translator\TranslatorInterface;

/**
 * This is the factory for UserController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class UserControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $connection=$container->get('ADO\Connection');
        $config = $container->get('Config');
        $userManager = $container->get(UserManager::class);
        $translator = $container->get(TranslatorInterface::class);

        return new $requestedName($connection, $userManager,$config,$translator);
    }
}