<?php
declare(strict_types=1);

namespace LazyHttpClientBundle\Tests\ReqresFakeApi\Query;

/**
 * Class ListUsersQuery
 */
class ListUsersQuery extends AbstractQuery
{
    /**
     * @return string
     */
    public function getUri(): string
    {
        return '/api/users';
    }
}
