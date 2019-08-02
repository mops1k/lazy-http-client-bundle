<?php
declare(strict_types=1);

namespace LazyHttpClientBundle\Client;

use LazyHttpClientBundle\Interfaces\ResponseInterface;

/**
 * Class AbstractResponse
 */
abstract class AbstractResponse implements ResponseInterface
{
    /**
     * @var string
     */
    protected $content;

    /**
     * @var array|string[][]
     */
    protected $headers;

    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
