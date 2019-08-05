<?php
declare(strict_types=1);

namespace LazyHttpClientBundle\Tests\ReqresFakeApi;

use LazyHttpClientBundle\Client\AbstractClient;

/**
 * Class Client
 */
class Client extends AbstractClient
{
    protected const BASE_URI = 'https://reqres.in';
}
