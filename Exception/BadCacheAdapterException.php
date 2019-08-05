<?php
declare(strict_types=1);

namespace LazyHttpClientBundle\Exception;

use LazyHttpClientBundle\Interfaces\ApiExceptionInterface;
use LazyHttpClientBundle\Interfaces\CacheAdapterInterface;
use Throwable;

/**
 * Class BadCacheAdapterException
 */
class BadCacheAdapterException extends \Exception implements ApiExceptionInterface
{
    public const CODE = 500;

    /**
     * ClientNotFoundException constructor.
     *
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($code = self::CODE, Throwable $previous = null)
    {
        parent::__construct(\sprintf(
            'Wrong cache adapter instance. Expected instance is "%s"',
            CacheAdapterInterface::class
        ), $code, $previous);
    }
}
