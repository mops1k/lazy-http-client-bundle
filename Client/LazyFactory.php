<?php
declare(strict_types=1);

namespace LazyHttpClientBundle\Client;

use LazyHttpClientBundle\Interfaces\ClientInterface;
use LazyHttpClientBundle\Interfaces\QueryInterface;
use LazyHttpClientBundle\Interfaces\ResponseInterface;
use ProxyManager\Factory\LazyLoadingGhostFactory;
use ProxyManager\Proxy\GhostObjectInterface;

/**
 * Class LazyFactory
 */
class LazyFactory
{
    /**
     * @var LazyLoadingGhostFactory
     */
    private $lazyFactory;

    /**
     * @var HttpQueue
     */
    private $apiPool;

    /**
     * @var ResponseInterface[]|GhostObjectInterface[]
     */
    private $responses = [];

    /**
     * LazyFactory constructor.
     *
     * @param HttpQueue $apiPool
     */
    public function __construct(HttpQueue $apiPool)
    {
        $this->lazyFactory  = new LazyLoadingGhostFactory();
        $this->apiPool      = $apiPool;
    }

    /**
     * Return not initialized proxy response
     *
     * @param QueryInterface $query
     *
     * @return GhostObjectInterface|ResponseInterface
     */
    public function create(ClientInterface $client): GhostObjectInterface
    {
        $key = $client->getCurrentQuery()->getHashKey();
        if (\array_key_exists($key, $this->responses)) {
            return $this->responses[$key];
        }

        $initializer = function (
            GhostObjectInterface $ghostObject,
            string $method,
            array $parameters,
            &$initializer,
            array $properties
        ) use ($key) {
            /** @var ResponseInterface|GhostObjectInterface $ghostObject */
            $initializer = null;

            $this->apiPool->execute();
            $response = $this->apiPool->getResponseForKey($key);

            $properties["\0*\0content"]    = $response['content'];
            $properties["\0*\0statusCode"] = $response['statusCode'];
            $properties["\0*\0headers"]    = $response['headers'];

            return true;
        };

        $this->responses[$key] = $this->lazyFactory->createProxy($client->getCurrentQuery()->getResponseClassName(), $initializer);

        return $this->responses[$key];
    }
}
