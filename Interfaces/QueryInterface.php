<?php
declare(strict_types=1);

namespace LazyHttpClientBundle\Interfaces;

use LazyHttpClientBundle\Client\Request;
use LazyHttpClientBundle\Interfaces\ClientInterface;

/**
 * Class QueryInterface
 */
interface QueryInterface
{
    /**
     * @return string
     */
    public function getMethod(): string;

    /**
     * @return Request
     */
    public function getRequest(): Request;

    /**
     * @return string
     */
    public function getUri(): string;

    /**
     * @return string
     */
    public function buildUri(): string;

    /**
     * @return string
     */
    public function getResponseClassName(): string;

    /**
     * @return string
     */
    public function getHashKey(): string;

    /**
     * @return ClientInterface
     */
    public function getClient(): ClientInterface;

    /**
     * @param ClientInterface $client
     *
     * @return static
     */
    public function setClient(ClientInterface $client);

    /**
     * @return array
     */
    public function supportedClients(): array;

    /**
     * @param ClientInterface $client
     *
     * @return bool
     */
    public function isSupport(ClientInterface $client): bool;
}
