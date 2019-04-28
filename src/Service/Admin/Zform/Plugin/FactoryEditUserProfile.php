<?php
namespace Mf\Users\Service\Admin\Zform\Plugin;

use Interop\Container\ContainerInterface;
use Mf\Users\Service\UserManager;
/*

*/

class FactoryEditUserProfile
{

public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
{
    $UserManager=$container->get(UserManager::class);
    return new $requestedName($UserManager);
}
}

