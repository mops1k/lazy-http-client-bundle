<?php
declare(strict_types=1);

namespace LazyHttpClientBundle\Exception;

use LazyHttpClientBundle\Interfaces\ApiExceptionInterface;
use Throwable;

/**
 * Class QueryNotFoundException
 */
class QueryNotFoundException extends \Exception implements ApiExceptionInterface
{
    public const CODE = 500;

    /**
     * ClientNotFoundException constructor.
     *
     * @param string         $clientClassName
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(string $clientClassName, $code = self::CODE, Throwable $previous = null)
    {
        parent::__construct(\sprintf(
            'No query FQCN "%s" are registered in api',
            $clientClassName
        ), $code, $previous);
    }
}
