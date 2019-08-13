Client example
=====

```php
<?php
declare(strict_types=1);

namespace App\ReqresFakeApi;

use LazyHttpClientBundle\Client\AbstractClient;

/**
 * Class Client
 */
class Client extends AbstractClient
{
    protected const BASE_URI = 'https://reqres.in';
}
```
