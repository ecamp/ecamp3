<?php

namespace eCamp\LibTest\PHPUnit;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use PHPUnit\Framework\TestCase;

abstract class AbstractDatabaseTestCase extends TestCase
{
    /**
     * @throws ToolsException
     */
    public function setUp() {
        parent::setUp();

        $em = $this->getEntityManager();
        $this->createDatabaseSchema($em);
    }

    public function tearDown() {
        parent::tearDown();

        \eCampApp::Reset();
    }

    /**
     * @param string $name
     * @return EntityManager
     */
    protected function getEntityManager($name = null) {
        $name = $name ?: 'orm_default';
        $name = 'doctrine.entitymanager.' . $name;

        return \eCampApp::GetService($name);
    }

    /**
     * @param EntityManager $em
     * @throws ToolsException
     */
    protected function createDatabaseSchema(EntityManager $em) {
        $metadatas = $em->getMetadataFactory()->getAllMetadata();

        $schemaTool = new SchemaTool($em);
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($metadatas);
    }

}
