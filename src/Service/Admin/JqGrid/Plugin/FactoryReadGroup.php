<?php
namespace Mf\Users\Service\Admin\JqGrid\Plugin;

use Interop\Container\ContainerInterface;
/*

*/

class FactoryReadGroup
{

public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
{
    $connection=$container->get('DefaultSystemDb');
    return new $requestedName($connection);
}
}

