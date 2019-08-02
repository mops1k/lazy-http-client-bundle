<?php
declare(strict_types=1);

namespace LazyHttpClientBundle\Client;

use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class ApiParameter
 */
final class Request
{
    /**
     * @var ParameterBag
     */
    private $headers;

    /**
     * @var ParameterBag
     */
    private $parameters;

    /**
     * @var string
     */
    private $body;

    /**
     * @var array
     */
    private $options = [];

    /**
     * ApiRequest constructor.
     */
    public function __construct()
    {
        $this->headers    = new HeaderBag();
        $this->parameters = new ParameterBag();
    }

    /**
     * @return ParameterBag
     */
    public function getHeaders(): HeaderBag
    {
        return $this->headers;
    }

    /**
     * @return ParameterBag
     */
    public function getParameters(): ParameterBag
    {
        return $this->parameters;
    }

    /**
     * @return string|null
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * @param string $body
     *
     * @return Request
     */
    public function setBody(string $body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     *
     * @return Request
     */
    public function setOptions(array $options): Request
    {
        $this->options = $options;

        return $this;
    }
}
