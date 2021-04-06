<?php

namespace FT\EzHealthCheckBundle\HealthChecks;

use Exception;
use Stash\Interfaces\PoolInterface;
use FT\HealthCheckBundle\HealthCheck\HealthCheck;
use FT\HealthCheckBundle\HealthCheck\HealthCheckHandlerInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Health check for testing eZ Persistence caches
 */
class PersistenceCacheHealthCheck implements HealthCheckHandlerInterface
{
    const HEALTH_CHECK_ID = 'PersistenceCacheHealthCheck';
    const HEALTH_CHECK_CACHE_KEY = 'health_check.ez.cache_test';
    const HEALTH_CHECK_CACHE_VALUE = 'A Test Value';
    const HEALTH_CHECK_TTL = 10;

    /**
     * @var PoolInterface
     */
    protected $cachePool;

    /**
     * @param PoolInterface $cachePool
     */
    public function __construct(CacheItemPoolInterface $cachePool)
    {
        $this->cachePool = $cachePool;
    }

    /**
     * {@inheritdoc}
     */
    public function runHealthCheck(): HealthCheck
    {
        $healthCheck = new HealthCheck();
        $ok = false;
        try {
            // Cycle between retrieving and setting the same cache item every few pings to this health check that caches are writable and readable. 
            // If either step fails it is assumed there is something majorly wrong with the caches and the check fails.
            $testCacheItem = $this->cachePool->getItem(self::HEALTH_CHECK_CACHE_KEY);
            if (!$testCacheItem->isHit()) {
                $testCacheItem->set(self::HEALTH_CHECK_CACHE_VALUE);
                $testCacheItem->expiresAfter(self::HEALTH_CHECK_TTL);
                $this->cachePool->save($testCacheItem);
                $ok = true;
            } else {
                if(!$ok = $testCacheItem->get() === self::HEALTH_CHECK_CACHE_VALUE){
                    $healthCheck->withCheckOutput('Mismatch in expected states between cache write and load');
                }
            }
        } catch (Exception $e){
            $healthCheck->withCheckOutputThrowable($e);
        }

        return $healthCheck
            ->withId(self::HEALTH_CHECK_ID)
            ->withName('Can the site use caches')
            ->withOk($ok)
            ->withSeverity(1)
            ->withPanicGuide('Check that the currently configured persistence cache is online and accessible by the server.')
            ->withTechnicalSummary('This healthcheck repeatedly reads and writes to a cache key (' . self::HEALTH_CHECK_CACHE_KEY. '). In the event the value cannot be retrieved or written to the healthcheck fails.')
            ->withBusinessImpact('Most users will not be able to visit the site. The admin area will go completely down. Large portions of the website be inaccessible.');
    }

    /**
     * {@inheritdoc}
     */
    public function getHealthCheckId(): string
    {
        return self::HEALTH_CHECK_ID;
    }

    /**
     * {@inheritdoc}
     */
    public function getHealthCheckInterval(): int
    {
        return 10;
    }
}
