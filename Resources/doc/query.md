Query example
=====
```php
<?php
declare(strict_types=1);

namespace App\ReqresFakeApi\Query;

use LazyHttpClientBundle\Client\AbstractQuery as BaseAbstractQuery;
use App\ReqresFakeApi\Client;
use App\ReqresFakeApi\StringResponse;

/**
 * Class AbstractQuery
 */
abstract class AbstractQuery extends BaseAbstractQuery
{
    /**
     * @return string
     */
    public function getUri(): string
    {
        return '/api/users';
    }

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
```