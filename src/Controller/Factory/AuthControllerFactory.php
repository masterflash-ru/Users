<?php
namespace Mf\Users\Controller\Factory;

use Interop\Container\ContainerInterface;

use Zend\ServiceManager\Factory\FactoryInterface;
use Mf\Permissions\Service\AuthManager;
use Zend\Validator\Translator\TranslatorInterface;

/**
 * This is the factory for AuthController. Its purpose is to instantiate the controller
 * and inject dependencies into its constructor.
 */
class AuthControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {   

        $authManager = $container->get(AuthManager::class);
        $config = $container->get("config");
        $translator = $container->get(TranslatorInterface::class);
        return new $requestedName($authManager,$config,$translator);
    }
}
