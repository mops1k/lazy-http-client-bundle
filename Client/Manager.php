<?php
declare(strict_types=1);

namespace LazyHttpClientBundle\Client;

use LazyHttpClientBundle\Exception\ClientNotFoundException;
use LazyHttpClientBundle\Interfaces\ClientInterface;

/**
 * Class ApiClientManager
 */
class Manager
{
    public const TAG = 'lazy_http_client.client';

    /**
     * @var ClientInterface[]
     */
    private $clients = [];

    /**
     * ApiClientManager constructor.
     *
     * @param iterable|null $clients
     */
    public function __construct(?iterable $clients)
    {
        if (!$clients) {
            return;
        }

        foreach ($clients as $client) {
            $this->clients[\get_class($client)] = $client;
        }
    }

    /**
     * @param string $name
     *
     * @return ClientInterface
     *
     * @throws ClientNotFoundException
     */
    public function get(string $name): ClientInterface
    {
        if (!isset($this->clients[$name])) {
            throw new ClientNotFoundException($name);
        }

        return $this->clients[$name];
    }
}
