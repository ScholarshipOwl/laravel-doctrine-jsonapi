<?php

namespace Sowl\JsonApi\Testing;

/**
 * Truncate all Doctrine database tables after each test.
 */
trait DoctrineDatabaseTruncation
{
    public function truncateDoctrineDatabaseTables(): void
    {
        $em = $this->app['em'];
        $connection = $em->getConnection();
        $platform = $connection->getDatabasePlatform();
        $cmd = $em->getClassMetadataFactory()->getAllMetadata();
        $connection->beginTransaction();
        try {
            foreach ($cmd as $meta) {
                $tableName = $meta->getTableName();
                $connection->executeStatement($platform->getTruncateTableSQL($tableName, true));
            }
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    }
}
