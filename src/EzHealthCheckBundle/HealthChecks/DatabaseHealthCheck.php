<?php

namespace FT\EzHealthCheckBundle\HealthChecks;

use Exception;
use Doctrine\ORM\EntityManager;
use eZ\Publish\API\Repository\Repository;
use FT\HealthCheckBundle\HealthCheck\HealthCheck;
use FT\HealthCheckBundle\HealthCheck\HealthCheckHandlerInterface;

/**
 * Healthcheck for testing eZ Database connection.
 */
class DatabaseHealthCheck implements HealthCheckHandlerInterface
{
    const HEALTH_CHECK_ID = 'DatabaseHealthCheck';

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param EntityManager $repository
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function runHealthCheck(): HealthCheck
    {
        $healthCheck = new HealthCheck();
        try {
            //Establish a connection with the database
            $this->entityManager->getConnection()->connect();
        } catch (Exception $e) {
            //Supress errors raised from trying to connect so we can log it as standard error to check our connection
        }

        //Check if that connection was properly established
        $ok = $this->entityManager->getConnection()->isConnected();

        if (!$ok) {
            $healthCheck->withCheckOutput('Failed to establish connection with the configured database');
        }

        return $healthCheck
            ->withId(self::HEALTH_CHECK_ID)
            ->withName('Can the site connect to the database')
            ->withOk($ok)
            ->withSeverity(1)
            ->withPanicGuide('Check that the application can connect to the configured database. Also check if the configured database is online.')
            ->withTechnicalSummary('This healthcheck tries to use the configured database connection (through the entity manager service) to establish a connection with the database.')
            ->withBusinessImpact('The site will go offline. Users will not be able to view any page on the site. The admin area will be inaccessible.');
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
        return 5;
    }
}
