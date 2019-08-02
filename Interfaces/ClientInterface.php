<?php
declare(strict_types=1);

namespace LazyHttpClientBundle\Interfaces;

use LazyHttpClientBundle\Interfaces\QueryInterface;
use LazyHttpClientBundle\Interfaces\ResponseInterface;
use ProxyManager\Proxy\GhostObjectInterface;

/**
 * Interface ClientInterface
 */
interface ClientInterface
{
    /**
     * @param string $queryClass
     *
     * @return void
     */
    public function use(string $queryClass): void;

    /**
     * @return string
     */
    public function getHost(): string;

    /**
     * @return QueryInterface
     */
    public function getCurrentQuery(): QueryInterface;

    /**
     * @return array
     */
    public function getOptions(): array;

    /**
     * @return GhostObjectInterface|ResponseInterface
     */
    public function execute(): GhostObjectInterface;
}
