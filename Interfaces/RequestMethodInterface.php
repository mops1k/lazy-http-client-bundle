<?php
declare(strict_types=1);

namespace LazyHttpClientBundle\Interfaces;

/**
 * Class Request
 */
interface RequestMethodInterface
{
    public const HEAD    = 'HEAD';
    public const GET     = 'GET';
    public const POST    = 'POST';
    public const PUT     = 'PUT';
    public const PATCH   = 'PATCH';
    public const DELETE  = 'DELETE';
    public const PURGE   = 'PURGE';
    public const OPTIONS = 'OPTIONS';
    public const TRACE   = 'TRACE';
    public const CONNECT = 'CONNECT';
}
