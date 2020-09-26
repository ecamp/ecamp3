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
    public function setUp() {
        parent::setUp();
        putenv('env=test');
        $em = $this->getEntityManager();
        $this->createDatabaseSchema($em);
    }

    public function tearDown() {
        parent::tearDown();

        \eCampApp::Reset();
    }

    /**
     * @param string $name
     *
     * @return EntityManager
     */
    protected function getEntityManager($name = 'orm_default') {
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
