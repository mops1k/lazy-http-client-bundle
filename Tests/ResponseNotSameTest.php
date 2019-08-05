<?php
declare(strict_types=1);

namespace LazyHttpClientBundle\Tests;

use LazyHttpClientBundle\Client\Manager;
use LazyHttpClientBundle\Tests\ReqresFakeApi\Client;
use LazyHttpClientBundle\Tests\ReqresFakeApi\Query\ListUsersQuery;
use LazyHttpClientBundle\Tests\ReqresFakeApi\Query\SingleUserQuery;
use LazyHttpClientBundle\Exception\ClientNotFoundException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class ResponseNotSameTest
 */
class ResponseNotSameTest extends KernelTestCase
{
    /**
     * @throws ClientNotFoundException
     */
    public function testResponse()
    {
        self::bootKernel();
        /** @var Manager $apiClientManager */
        $apiClientManager = self::$container->get(Manager::class);
        $client  = $apiClientManager->get(Client::class);
        $client->use(ListUsersQuery::class);
        $listResult = $client->execute();

        $client->use(ListUsersQuery::class);
        $request = $client->getCurrentQuery()->getRequest();
        $request->getParameters()->set('page', 2);
        $listResult2 = $client->execute();

        $client->use(ListUsersQuery::class);
        $request = $client->getCurrentQuery()->getRequest();
        $request->getParameters()->set('page', 2);
        $listResult3 = $client->execute();

        $client->use(SingleUserQuery::class);
        $request = $client->getCurrentQuery()->getRequest();
        $request->getParameters()->set('id', 11);
        $singleUserResult = $client->execute();

        $this->assertNotSame($listResult, $singleUserResult);
        $this->assertNotSame($listResult, $listResult2);
        $this->assertSame($listResult2, $listResult3);
        $this->assertNotEquals($listResult->getContent(), $singleUserResult->getContent());
    }
}
