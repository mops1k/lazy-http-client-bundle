Lazy Http Client Bundle
=====
This bundle provides lazy http client for symfony 4.1 and above.
Lazy means that before you are not using any response methods, request doesn't execute.

#### Instructions
- [Installation](Resources/doc/installation.md)
- [Example client](Resources/doc/client.md)
- [Example response](Resources/doc/response.md)
- [Example query](Resources/doc/query.md)

#### Usage
Simple usage example

```php
use LazyHttpClientBundle\Client\Manager;
use App\ReqresFakeApi\Client;
use App\ReqresFakeApi\Query\ListUsersQuery;
use App\ReqresFakeApi\Query\SingleUserQuery;

$client = $this->get(Manager::class)->get(Client::class);
$client->use(ListUsersQuery::class);
$listResult = $client->execute();

$client = $this->apiClientManager->get(Client::class);
$client->use(ListUsersQuery::class);
$request = $client->getCurrentQuery()->getRequest();
$request->getParameters()->set('page', 2);
$listResult2 = $client->execute();

echo $listResult->getContent();
echo $listResult2->getContent();
echo $listResult2->getStatusCode();

```
