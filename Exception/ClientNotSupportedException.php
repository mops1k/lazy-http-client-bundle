<?php
declare(strict_types=1);

namespace LazyHttpClientBundle\Exception;

use LazyHttpClientBundle\Interfaces\ApiExceptionInterface;
use LazyHttpClientBundle\Interfaces\ClientInterface;
use LazyHttpClientBundle\Interfaces\QueryInterface;
use Throwable;

/**
 * Class ClientNotSupportedException
 */
class ClientNotSupportedException extends \Exception implements ApiExceptionInterface
{
    public const CODE = 400;

    /**
     * ClientNotFoundException constructor.
     *
     * @param ClientInterface $client
     * @param QueryInterface  $query
     * @param int             $code
     * @param Throwable|null  $previous
     */
    public function __construct(ClientInterface $client, QueryInterface $query, $code = self::CODE, Throwable $previous = null)
    {
        parent::__construct(\sprintf(
            'Client "%s" are not supported in query "%s"',
            get_class($client),
            get_class($query)
        ), $code, $previous);
    }
}
