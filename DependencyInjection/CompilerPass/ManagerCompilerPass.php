<?php
declare(strict_types=1);

namespace LazyHttpClientBundle\DependencyInjection\CompilerPass;

use LazyHttpClientBundle\Client\Manager;
use LazyHttpClientBundle\Interfaces\ClientInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class ManagerCompilerPass
 */
class ManagerCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     */
    public function process(ContainerBuilder $container)
    {
        $container->registerForAutoconfiguration(ClientInterface::class)->addTag(Manager::TAG);

        $serviceIds = $container->findTaggedServiceIds(Manager::TAG, true);
        $services   = [];
        foreach ($serviceIds as $serviceId => $additionalData) {
            $services[] = $container->getDefinition($serviceId);
        }

        $container->getDefinition(Manager::class)->setArguments([$services]);
    }
}
