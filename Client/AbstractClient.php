<?php
declare(strict_types=1);

namespace LazyHttpClientBundle\Client;

use LazyHttpClientBundle\Exception\ClientNotSupportedException;
use LazyHttpClientBundle\Exception\QueryNotFoundException;
use LazyHttpClientBundle\Interfaces\ClientInterface;
use LazyHttpClientBundle\Interfaces\QueryInterface;
use LazyHttpClientBundle\Interfaces\ResponseInterface;
use ProxyManager\Proxy\GhostObjectInterface;

/**
 * Class AbstractClient
 */
abstract class AbstractClient implements ClientInterface
{
    protected const BASE_URI = '';

    /**
     * @var QueryInterface
     */
    public $query;

    /**
     * @var QueryContainer
     */
    private $queryContainer;

    /**
     * @var HttpQueue
     */
    private $pool;

    /**
     * @var LazyFactory
     */
    private $lazyFactory;

    /**
     * Client constructor.
     *
     * @param QueryContainer $queryContainer
     * @param HttpQueue      $pool
     * @param LazyFactory    $lazyFactory
     */
    public function __construct(QueryContainer $queryContainer, HttpQueue $pool, LazyFactory $lazyFactory)
    {
        $this->queryContainer = $queryContainer;
        $this->pool = $pool;
        $this->lazyFactory = $lazyFactory;
    }

    /**
     * @param string $queryClass
     *
     * @return void
     *
     * @throws ClientNotSupportedException
     * @throws QueryNotFoundException
     */
    public function use(string $queryClass): void
    {
        $this->query = clone $this->queryContainer->get($queryClass);

        if (!$this->query->isSupport($this)) {
            throw new ClientNotSupportedException($this, $this->query);
        }

        $this->query->setClient($this);
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return static::BASE_URI;
    }

    /**
     * @return GhostObjectInterface|ResponseInterface
     */
    public function execute(): GhostObjectInterface
    {
        $clonedClient = clone $this;
        $this->pool->add($clonedClient->query);

        return $this->lazyFactory->create($clonedClient);
    }

    /**
     * @return QueryInterface
     */
    public function getCurrentQuery(): QueryInterface
    {
        return $this->query;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return [];
    }
}
