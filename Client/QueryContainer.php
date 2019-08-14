<?php
declare(strict_types=1);

namespace LazyHttpClientBundle\Client;

use LazyHttpClientBundle\Exception\QueryNotFoundException;
use LazyHttpClientBundle\Interfaces\QueryInterface;

/**
 * Class QueryContainerInterface
 */
class QueryContainer
{
    public const TAG = 'lazy_http_client.query';

    /**
     * @var QueryInterface[]
     */
    private $collection = [];

    /**
     * ApiClientManager constructor.
     *
     * @param iterable|null $clients
     */
    public function __construct(?iterable $queries)
    {
        if (!$queries) {
            return;
        }

        foreach ($queries as $query) {
            if ($query instanceof QueryInterface) {
                $this->collection[\get_class($query)] = $query;
            }
        }
    }

    /**
     * @param string $name
     *
     * @return QueryInterface
     *
     * @throws QueryNotFoundException
     */
    public function get(string $name): QueryInterface
    {
        if (!\array_key_exists($name, $this->collection)) {
            throw new QueryNotFoundException($name);
        }

        return $this->collection[$name];
    }
}
