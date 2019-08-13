<?php
declare(strict_types=1);

namespace LazyHttpClientBundle\Client;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\TransferStats;
use LazyHttpClientBundle\Exception\BadCacheAdapterException;
use LazyHttpClientBundle\Exception\ResponseNotFoundException;
use LazyHttpClientBundle\Interfaces\CacheAdapterInterface;
use LazyHttpClientBundle\Interfaces\QueryInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Request;
use LazyHttpClientBundle\Profiler\RequestCollector;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Class ApiPool
 */
class HttpQueue
{
    /**
     * @var QueryInterface[]
     */
    protected $pool = [];

    /**
     * @var Client[]
     */
    protected $httpClients = [];

    /**
     * @var array
     */
    protected $responses = [];

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    protected $requestsInfo = [];

    /**
     * @var RequestCollector
     */
    protected $requestCollector;

    /**
     * @var CacheAdapterInterface|null
     */
    protected $cacheAdapter;

    /**
     * HttpQueue constructor.
     *
     * @param LoggerInterface            $logger
     * @param RequestCollector           $requestCollector
     * @param CacheAdapterInterface|null $cacheAdapter
     *
     * @throws BadCacheAdapterException
     */
    public function __construct(LoggerInterface $logger, RequestCollector $requestCollector, ?CacheAdapterInterface $cacheAdapter)
    {
        $this->logger           = $logger;
        $this->requestCollector = $requestCollector;
        if ($cacheAdapter) {
            if (!$cacheAdapter instanceof CacheAdapterInterface) {
                throw new BadCacheAdapterException();
            }
            $this->cacheAdapter = $cacheAdapter;
        }
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

            if (null !== $this->cacheAdapter) {
                $this->cacheAdapter->setKey($key);
                if ($this->cacheAdapter->isHit() && !$query->getRequest()->isCacheForced()) {
                    $this->responses[$key] = $this->cacheAdapter->get();
                    continue;
                }
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

            $cacheTtl = -1;
            if (null !== $this->cacheAdapter) {
                $cacheTtl = $request->getCacheTtl();
            }

            $promise = $this->httpClients[\get_class($query->getClient())]->sendAsync($httpRequest, array_merge($query->getRequest()->getOptions(), [
                RequestOptions::ON_STATS => function (TransferStats $stats) use ($key) {
                    $this->requestsInfo[$key]['timing']     = \round($stats->getTransferTime(), 3);
                    $this->requestsInfo[$key]['statusCode'] = $stats->hasResponse() ? $stats->getResponse()->getStatusCode() : 500;
                }
            ]));

            $promise->then(function (ResponseInterface $response) use ($key, $cacheTtl) {
                $this->responses[$key] = [
                    'headers'    => $response->getHeaders(),
                    'statusCode' => $response->getStatusCode(),
                    'content'    => $response->getBody()->getContents(),
                ];
                if (null !== $this->cacheAdapter) {
                    $this->cacheAdapter->save($this->responses[$key], $cacheTtl);
                }

                $this->requestCollector->collectInfo($this->requestsInfo[$key]);
                $this->logger->info('Request success!', $this->requestsInfo[$key]);
            }, function (ClientException $reason) use ($key) {
                $response = $reason->getResponse();
                $this->responses[$key] = [
                    'headers'    => $response ? $response->getHeaders() : [],
                    'statusCode' => $response ? $response->getStatusCode() : $reason->getCode(),
                    'content'    => $response ? $response->getBody()->getContents() : $reason->getMessage(),
                ];

                $this->requestCollector->collectInfo($this->requestsInfo[$key]);
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
