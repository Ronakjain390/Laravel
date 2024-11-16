<?php

namespace App\Cache;

use Illuminate\Cache\Repository;
use Illuminate\Contracts\Cache\Store;

class LruCache extends Repository
{
    protected $lruCache = [];
    protected $maxSize;

    public function __construct(Store $store, $maxSize = 1000)
    {
        parent::__construct($store);
        $this->maxSize = $maxSize;
    }

    public function put($key, $value, $ttl = null)
    {
        // Remove the least recently used item if the cache is full
        if (count($this->lruCache) >= $this->maxSize) {
            $leastRecentKey = array_key_first($this->lruCache);
            $this->forget($leastRecentKey);
            unset($this->lruCache[$leastRecentKey]);
        }

        // Store the item in the cache
        $minutes = $ttl instanceof \DateInterval ? $this->getMinutes($ttl) : $ttl;
        parent::put($key, $value, $minutes);

        // Update the LRU cache
        $this->lruCache[$key] = time();
    }

    public function get($key, $default = null): mixed
    {
        // Get the item from the cache
        $value = parent::get($key, $default);

        // Update the LRU cache
        if (isset($this->lruCache[$key])) {
            $this->lruCache[$key] = time();
        }

        return $value;
    }

    protected function getMinutes($ttl)
    {
        $interval = $ttl instanceof \DateInterval ? $ttl : \DateInterval::createFromDateString($ttl);

        return $interval->days * 24 * 60 + $interval->h * 60 + $interval->i;
    }
}
