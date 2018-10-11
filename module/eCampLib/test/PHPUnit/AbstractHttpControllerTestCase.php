<?php

namespace eCamp\LibTest\PHPUnit;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use Zend\Authentication\AuthenticationService;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase as ZendAbstractHttpControllerTestCase;

abstract class AbstractHttpControllerTestCase extends ZendAbstractHttpControllerTestCase {
    /**
     * @throws ToolsException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function setUp() {
        parent::setUp();

        $data = include __DIR__ . '/../../../../config/application.config.php';
        $this->setApplicationConfig($data);

        $em = $this->getEntityManager();
        $this->createDatabaseSchema($em);
    }

    protected function getService($name) {
        return $this->getApplicationServiceLocator()->get($name);
    }

    /** @return EntityManager */
    protected function getEntityManager($name = null) {
        $name = $name ?: 'orm_default';
        $name = 'doctrine.entitymanager.' . $name;

        return $this->getService($name);
    }

    /** @return \Doctrine\ORM\EntityRepository */
    protected function getRepository($entityName, $name = null) {
        return $this->getEntityManager($name)->getRepository($entityName);
    }

    protected function getRandomEntity($entityName, $name = null) {
        $repo = $this->getRepository($entityName, $name);
        $entities = $repo->findAll();

        if(count($entities)){
            $idx = array_rand($entities, 1);
            return $entities[$idx];
        }

        return null;
    }

    protected function login($userId) {
        $authService = new AuthenticationService();
        $authService->getStorage()->write($userId);
    }



    /** @throws ToolsException */
    protected function createDatabaseSchema(EntityManager $em) {
        $metadatas = $em->getMetadataFactory()->getAllMetadata();

        $schemaTool = new SchemaTool($em);
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($metadatas);
    }

    protected function createDummyData($name = null) {
        $em = $this->getEntityManager($name);
        $loader = new \Doctrine\Common\DataFixtures\Loader();

        $paths = \Zend\Stdlib\Glob::glob(__DIR__ . "/../../../*/data/prod/*.php");
        foreach ($paths as $path) { $loader->loadFromFile($path); }

        $paths = \Zend\Stdlib\Glob::glob(__DIR__ . "/../../../*/data/dev/*.php");
        foreach ($paths as $path) { $loader->loadFromFile($path); }

        $executor = new \Doctrine\Common\DataFixtures\Executor\ORMExecutor(
            $em, new \Doctrine\Common\DataFixtures\Purger\ORMPurger()
        );
        $executor->execute($loader->getFixtures(), true);
    }
}
