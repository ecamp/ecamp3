<?php

namespace eCamp\LibTest\PHPUnit;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
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

    /**
     * loads data from Fixtures into ORM.
     */
    protected function loadFixtures(Loader $loader) {
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->getEntityManager(), $purger);
        $executor->execute($loader->getFixtures());
    }
}
