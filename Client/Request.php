<?php
declare(strict_types=1);

namespace LazyHttpClientBundle\Client;

use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class ApiParameter
 *
 * @method void clear()
 * @method void clearOptions()
 * @method void clearParameters()
 * @method void clearFormData()
 * @method void clearHeaders()
 * @method void clearBody()
 * @method void clearCacheTtl()
 * @method void clearCacheForced()
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
     * @var ParameterBag
     */
    private $options;

    /**
     * @var int
     */
    private $cacheTtl = -1;

    /**
     * @var bool
     */
    private $isCacheForced = false;

    /**
     * @var ParameterBag
     */
    private $formData;

    /**
     * ApiRequest constructor.
     */
    public function __construct()
    {
        $this->headers    = new HeaderBag();
        $this->parameters = new ParameterBag();
        $this->options    = new ParameterBag();
        $this->formData   = new ParameterBag();
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
    public function getOptions(): ParameterBag
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
        $this->options->add($options);

        return $this;
    }

    /**
     * @return int
     */
    public function getCacheTtl(): int
    {
        return $this->cacheTtl;
    }

    /**
     * @param int $cacheTtl
     *
     * @return Request
     */
    public function setCacheTtl(int $cacheTtl): Request
    {
        $this->cacheTtl = $cacheTtl;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCacheForced(): bool
    {
        return $this->isCacheForced;
    }

    /**
     * @param bool $isCacheForced
     *
     * @return Request
     */
    public function setIsCacheForced(bool $isCacheForced): Request
    {
        $this->isCacheForced = $isCacheForced;

        return $this;
    }

    /**
     * @return ParameterBag
     */
    public function getFormData(): ParameterBag
    {
        return $this->formData;
    }

    /**
     * @param $name
     * @param $arguments
     */
    public function __call($name, $arguments)
    {
        if (\strpos($name, 'clear') !== 0) {
            return;
        }

        if (\strlen($name) === 5) {
            $this->options = new ParameterBag();
            $this->parameters = new ParameterBag();
            $this->headers = new HeaderBag();
            $this->cacheTtl = -1;
            $this->body = null;
            $this->isCacheForced = false;

            return;
        }

        $parameterToClear = \substr($name, 5);

        switch ($parameterToClear) {
            case 'Options':
            case 'Parameters':
            case 'FormData':
                $this->{\lcfirst($parameterToClear)} = new ParameterBag();
                break;
            case 'Headers':
                $this->headers = new HeaderBag();
                break;
            case 'Body':
                $this->body = null;
                break;
            case 'CacheTtl':
                $this->cacheTtl = -1;
                break;
            case 'CacheForced':
                $this->isCacheForced = false;
                break;
        }
    }
}
