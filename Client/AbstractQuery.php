<?php
declare(strict_types=1);

namespace LazyHttpClientBundle\Client;

use LazyHttpClientBundle\Interfaces\ClientInterface;
use LazyHttpClientBundle\Interfaces\QueryInterface;
use LazyHttpClientBundle\Interfaces\RequestMethodInterface;
use LazyHttpClientBundle\Client\Request;

/**
 * Class AbstractQuery
 */
abstract class AbstractQuery implements QueryInterface
{
    /**
     * @var Request
     */
    protected $apiRequest;

    /**
     * @var string
     */
    private $buildedUri;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * Supported clients for query
     *
     * @return array
     */
    public function supportedClients(): array
    {
        return [];
    }

    /**
     * Request for query
     *
     * @return Request
     */
    public function getRequest(): Request
    {
        if (!$this->apiRequest) {
            $this->apiRequest = new Request();
            $this->apiRequest->getHeaders()->set('Content-Type', 'application/json');
        }

        return $this->apiRequest;
    }

    /**
     * Set Query method
     *
     * @return string
     */
    public function getMethod(): string
    {
        return RequestMethodInterface::GET;
    }

    /**
     * Building uri for query
     *
     * @return string
     */
    public function buildUri(): string
    {
        if (!$this->buildedUri) {
            $parameters = $this->getRequest()->getParameters()->all();
            $uri = $this->getUri();
            foreach ($parameters as $name => $value) {
                if (preg_match('#\{'.$name.'\}#i', $uri)) {
                    $uri = str_replace('{'.$name.'}', $value, $uri);
                    $this->getRequest()->getParameters()->remove($name);
                }
            }

            $this->body = $this->getRequest()->getParameters()->all();
            $uriParameters = null;
            if ($this->getMethod() === RequestMethodInterface::GET && $this->getRequest()->getParameters()->all()) {
                $uriParameters = '?'.\http_build_query($this->getRequest()->getParameters()->all());
            }

            $this->buildedUri = $uri.$uriParameters;
        }

        return $this->buildedUri;
    }

    /**
     * GenerateHashKey
     *
     * @return string
     */
    public function getHashKey(): string
    {
        return hash('sha256', \json_encode(\array_merge(
            [
                $this->getUri(),
                $this->getRequest()->getBody(),
            ],
            $this->getRequest()->getParameters()->all(),
            $this->getRequest()->getHeaders()->all(),
            $this->getRequest()->getOptions()
        )));
    }

    /**
     * Get client
     *
     * @return ClientInterface
     */
    public function getClient(): ClientInterface
    {
        return $this->client;
    }

    /**
     * Set client (internal method)
     *
     * @param ClientInterface $client
     *
     * @return static
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Check if query is support client where its executed
     *
     * @param ClientInterface $client
     *
     * @return bool
     */
    public function isSupport(ClientInterface $client): bool
    {
        return !$this->supportedClients() || \in_array(\get_class($client), $this->supportedClients(), true);
    }
}
