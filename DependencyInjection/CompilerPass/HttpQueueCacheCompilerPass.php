<?php
declare(strict_types=1);

namespace LazyHttpClientBundle\DependencyInjection\CompilerPass;

use LazyHttpClientBundle\Client\HttpQueue;
use LazyHttpClientBundle\Interfaces\CacheAdapterInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class HttpQueueCacheCompilerPass
 */
class HttpQueueCacheCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     */
    public function process(ContainerBuilder $container)
    {
        $config = $container->getParameter('lazy_http_client');

        if (null !== $config['cache_adapter']) {
            $cacheAdapter = $container->getDefinition($config['cache_adapter']);
            $container->getDefinition(HttpQueue::class)->setArgument(2, $cacheAdapter);
        }
    }
}
