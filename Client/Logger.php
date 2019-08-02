<?php
declare(strict_types=1);

namespace LazyHttpClientBundle\Client;

use Monolog\Logger as BaseLogger;

/**
 * Class Logger
 */
class Logger extends BaseLogger
{
    public function __construct(array $handlers = [], array $processors = [])
    {
        parent::__construct('lazy_http_client', $handlers, $processors);
    }
}
