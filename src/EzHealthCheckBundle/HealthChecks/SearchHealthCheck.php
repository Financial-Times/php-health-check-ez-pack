<?php

namespace FT\EzHealthCheckBundle\HealthChecks;

use eZ\Publish\API\Repository\Repository;
use FT\HealthCheckBundle\HealthCheck\HealthCheck;
use FT\HealthCheckBundle\HealthCheck\HealthCheckHandlerInterface;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LocationId;

/**
 * Health check for testing eZ Search integration.
 */
class SearchHealthCheck implements HealthCheckHandlerInterface
{
    const HEALTH_CHECK_ID = 'SearchHealthCheck';
    const ROOT_NODE_ID = 2;

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @param Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function runHealthCheck(): HealthCheck
    {
        $ok = true;
        $healthCheck = new HealthCheck();
        $severity = 1;
        $searchService = $this->repository->getSearchService();
        try {
            //Attempt to search for the root content. This should always pass.
            $this->repository->sudo(function () use ($searchService) {
                $searchService->findSingle(new LocationId(self::ROOT_NODE_ID));
            });
        } catch (\Exception $e) {
            //If this fails we can be pretty sure something is seriously wrong
            $ok = false;

            //Switch get exceptions so we don't have to depend on them (just capture them if they exist)
            if (\get_class($e) === 'EzSystems\EzPlatformSolrSearchEngine\Gateway\HttpClient\ConnectionException') {
                $healthCheck->withCheckOutput('Application failed to connect to configured solr instance');
            } else {
                //Downgrade severity if it is unknown what is causing the issue
                $severity = 2;
                $healthCheck->withCheckOutputException($e);
            }
        }

        return $healthCheck
            ->withId(self::HEALTH_CHECK_ID)
            ->withName('Is search engine available')
            ->withOk($ok)
            ->withSeverity($severity)
            ->withPanicGuide('Check that the application can connect to the configured search engine. Also check if the configured search engine is online.')
            ->withTechnicalSummary('This health check queries for the root node (ID: ' . self::ROOT_NODE_ID . ') in the content tree. If this cannot be found it is assumed that the configured search engine is down.')
            ->withBusinessImpact('Users of the site will not be able to search for content. Many parts of the admin area will be unusable.');
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
