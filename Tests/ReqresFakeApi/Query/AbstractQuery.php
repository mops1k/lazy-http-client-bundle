<?php
declare(strict_types=1);

namespace LazyHttpClientBundle\Tests\ReqresFakeApi\Query;

use LazyHttpClientBundle\Client\AbstractQuery as BaseAbstractQuery;
use LazyHttpClientBundle\Tests\ReqresFakeApi\Client;
use LazyHttpClientBundle\Tests\ReqresFakeApi\StringResponse;

/**
 * Class AbstractQuery
 */
abstract class AbstractQuery extends BaseAbstractQuery
{
    /**
     * @return string
     */
    public function getResponseClassName(): string
    {
        return StringResponse::class;
    }

    /**
     * @return array
     */
    public function supportedClients(): array
    {
        return [Client::class];
    }
}
