<?php
declare(strict_types=1);

namespace LazyHttpClientBundle\Interfaces;

/**
 * Class CacheAdapterInterface
 */
interface CacheAdapterInterface
{
    /**
     * Return if cacheItem is exists
     *
     * @return bool
     */
    public function isHit(): bool;

    /**
     * Return saved cache
     *
     * @return array
     */
    public function get();

    /**
     * Save data for key
     *
     * @param mixed $data
     * @param int   $ttl
     *
     * @return static
     */
    public function save(array $data, int $ttl = -1);

    /**
     * Delete data for key
     *
     * @return static
     */
    public function delete();

    /**
     * Generate key for cache
     *
     * @param array $params
     *
     * @return string
     */
    public function generateKey(array $params = []): string;

    /**
     * Set current cache key
     *
     * @param string $key
     *
     * @return mixed
     */
    public function setKey(string $key);
}
