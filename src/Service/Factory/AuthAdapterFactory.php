<?php
namespace Mf\Users\Service\Factory;

use Interop\Container\ContainerInterface;
use Mf\Users\Service\AuthAdapter;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
фабрика адаптера авторизацйии
 */
class AuthAdapterFactory implements FactoryInterface
{
    /**
     * собсвтенно генератор объекта адаптера генератора, передаем в сам объект соединение с базой
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $connection=$container->get('DefaultSystemDb');
        $config=$container->get('config');
        return new AuthAdapter($connection,$config);
    }
}
