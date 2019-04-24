<?php
namespace Mf\Users\Service\Admin\JqGrid\Plugin;

use Interop\Container\ContainerInterface;


class FactoryUserStatus
{

public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
{
    $config=$container->get('config');
    return new $requestedName($config["users"]["users_status"]);
}
}

