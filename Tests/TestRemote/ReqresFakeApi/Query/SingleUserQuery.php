<?php
declare(strict_types=1);

namespace App\ReqresFakeApi\Query;

/**
 * Class SingleUserQuery
 */
class SingleUserQuery extends AbstractQuery
{
    /**
     * @return string
     */
    public function getUri(): string
    {
        return '/api/users/{id}';
    }
}
