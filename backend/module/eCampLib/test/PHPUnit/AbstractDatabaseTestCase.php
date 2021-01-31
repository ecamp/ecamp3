<?php

namespace eCamp\LibTest\PHPUnit;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use PHPUnit\Framework\TestCase;

abstract class AbstractDatabaseTestCase extends TestCase {
    /**
     * @throws ToolsException
     */
    public function setUp(): void {
        parent::setUp();

        $em = $this->getEntityManager();
        $this->createDatabaseSchema($em);
    }

    public function tearDown(): void {
        parent::tearDown();

        \eCampApp::Reset();
    }

    protected function getEntityManager(string $name = 'orm_default'): EntityManager {
        return \eCampApp::GetEntityManager($name);
    }

    /**
     * @throws ToolsException
     */
    protected function createDatabaseSchema(EntityManager $em) {
        $metadatas = $em->getMetadataFactory()->getAllMetadata();

        $schemaTool = new SchemaTool($em);
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($metadatas);
    }
}
