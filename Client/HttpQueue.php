<?php
declare(strict_types=1);

namespace LazyHttpClientBundle\Client;

use GuzzleHttp\Exception\ClientException;
use LazyHttpClientBundle\Exception\ResponseNotFoundException;
use LazyHttpClientBundle\Interfaces\QueryInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class ApiPool
 */
class HttpQueue
{
    /**
     * @var QueryInterface[]
     */
    private $pool = [];

    /**
     * @var Client[]
     */
    private $httpClients = [];

    /**
     * @var array
     */
    private $responses = [];

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var array
     */
    private $requestsInfo = [];

    /**
     * HttpQueue constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Add query to pool
     *
     * @param QueryInterface $query
     */
    public function add(QueryInterface $query): void
    {
        $this->pool[$query->getHashKey()] = $query;
    }

    /**
     * Execute queue requests pool
     */
    public function execute(): void
    {
        /** @var PromiseInterface[] $promises */
        $promises = [];
        foreach ($this->pool as $key => $query) {
            if (\array_key_exists($key, $this->responses)) {
                continue;
            }

            $uri = $query->buildUri();
            if (!\array_key_exists(\get_class($query->getClient()), $this->httpClients)) {
                $this->httpClients[\get_class($query->getClient())] = new Client(\array_merge([
                    'base_uri' => $query->getClient()->getHost()
                ], $query->getClient()->getOptions()));
            }

            $request = $query->getRequest();
            $httpRequest = new Request($query->getMethod(), $uri, $request->getHeaders()->all(), $request->getBody());

            $this->requestsInfo[$key] = [
                'host'       => $query->getClient()->getHost(),
                'uri'        => $uri,
                'method'     => $query->getMethod(),
                'parameters' => $request->getParameters()->all(),
                'headers'    => $request->getHeaders()->all(),
                'body'       => $request->getBody(),
            ];

            $promise = $this->httpClients[\get_class($query->getClient())]->sendAsync($httpRequest, $query->getRequest()->getOptions());
            $promise->then(function (ResponseInterface $response) use ($key) {
                $this->responses[$key] = [
                    'headers'    => $response->getHeaders(),
                    'statusCode' => $response->getStatusCode(),
                    'content'    => $response->getBody()->getContents(),
                ];

                $this->logger->info('Request success!', $this->requestsInfo[$key]);
            }, function (ClientException $reason) use ($key) {
                $response = $reason->getResponse();
                $this->responses[$key] = [
                    'headers'    => $response ? $response->getHeaders() : [],
                    'statusCode' => $response ? $response->getStatusCode() : $reason->getCode(),
                    'content'    => $response ? $response->getBody()->getContents() : $reason->getMessage(),
                ];

                $this->logger->error('Request failed!', \array_merge($this->requestsInfo[$key], [
                    'statusCode' => $response ? $response->getStatusCode() : $reason->getCode(),
                    'reason'     => $reason->getMessage(),
                ]));
            });
            $promises[] = $promise;
        }

        foreach ($promises as $promise) {
            $promise->wait(false);
        }

        // clear pool and clients after execute
        $this->pool = [];
        $this->httpClients = [];
    }

    /**
     * Returns response for executed query key
     *
     * @param string $key
     *
     * @return array
     *
     * @throws ResponseNotFoundException
     */
    public function getResponse(string $key): array
    {
        if (!\array_key_exists($key, $this->responses)) {
            throw new ResponseNotFoundException();
        }

        return $this->responses[$key];
    }

    /**
     * Return request information
     *
     * @param string $key
     *
     * @return array|null
     */
    public function getRequestInfo(string $key): ?array
    {
        return $this->requestsInfo[$key] ?? null;
    }
}
