<?php
namespace Mf\Users\Service\Admin\Zform\Plugin;

use Interop\Container\ContainerInterface;

/*

*/

class FactoryGetUserStatus
{

public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
{
	$config=$container->get("config");
    return new $requestedName($config["users"]);
}
}

