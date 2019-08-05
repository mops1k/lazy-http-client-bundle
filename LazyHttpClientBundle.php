<?php
declare(strict_types=1);

namespace LazyHttpClientBundle;

use LazyHttpClientBundle\DependencyInjection\CompilerPass\HttpQueueCacheCompilerPass;
use LazyHttpClientBundle\DependencyInjection\CompilerPass\ManagerCompilerPass;
use LazyHttpClientBundle\DependencyInjection\CompilerPass\QueryContainerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class LazyHttpClientBundle
 */
class LazyHttpClientBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ManagerCompilerPass());
        $container->addCompilerPass(new QueryContainerCompilerPass());
        $container->addCompilerPass(new HttpQueueCacheCompilerPass());
    }
}
