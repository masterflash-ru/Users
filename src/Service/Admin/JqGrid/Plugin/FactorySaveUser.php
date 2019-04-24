<?php
namespace Mf\Users\Service\Admin\JqGrid\Plugin;

use Interop\Container\ContainerInterface;
use Mf\Users\Service\UserManager;

class FactorySaveUser
{

public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
{
    $connection=$container->get('DefaultSystemDb');
    $UserManager=$container->get(UserManager::class);
    return new $requestedName($connection,$UserManager);
}
}

