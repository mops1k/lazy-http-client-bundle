<?php
declare(strict_types=1);

namespace LazyHttpClientBundle\Exception;

use LazyHttpClientBundle\Interfaces\ApiExceptionInterface;
use Throwable;

/**
 * Class ResponseNotFoundException
 */
class ResponseNotFoundException extends \Exception implements ApiExceptionInterface
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
        parent::__construct('Response for query are not found!', $code, $previous);
    }
}
