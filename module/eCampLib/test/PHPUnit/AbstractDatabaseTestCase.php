<?php

namespace eCamp\LibTest\PHPUnit;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;

abstract class AbstractDatabaseTestCase extends AbstractTestCase
{

    /**
     * @throws ToolsException
     */
    public function setUp() {
        include_once __DIR__ . '/../../../eCampApp.php';

        putenv('env=test');

        $em = $this->getEntityManager();
        $metadatas = $em->getMetadataFactory()->getAllMetadata();

        $schemaTool = new SchemaTool($em);
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($metadatas);

        parent::setUp();
    }

    /**
     * @param string $name
     * @return EntityManager
     */
    protected function getEntityManager($name = null) {
        $name = $name ?: 'orm_default';
        return \eCampApp::GetService('doctrine.entitymanager.' . $name);
    }

}
