<?php
declare(strict_types=1);

namespace LazyHttpClientBundle\DependencyInjection\CompilerPass;

use LazyHttpClientBundle\Client\QueryContainer;
use LazyHttpClientBundle\Interfaces\QueryInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class QueryContainerCompilerPass
 */
class QueryContainerCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     */
    public function process(ContainerBuilder $container)
    {
        $container->registerForAutoconfiguration(QueryInterface::class)->addTag(QueryContainer::TAG);

        $serviceIds = $container->findTaggedServiceIds(QueryContainer::TAG, true);
        $services   = [];
        foreach ($serviceIds as $serviceId => $additionalData) {
            $services[] = $container->getDefinition($serviceId);
        }

        $container->getDefinition(QueryContainer::class)->setArguments([$services]);
    }
}
