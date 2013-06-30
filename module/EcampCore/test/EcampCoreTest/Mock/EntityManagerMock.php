<?php

namespace EcampCoreTest\Mock;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Service\AbstractFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

class EntityManagerMockFactory extends AbstractFactory
{
    public function __construct()
    {
        parent::__construct('orm_default');
    }

    public function createService(ServiceLocatorInterface $sl)
    {
        /* @var $options \DoctrineORMModule\Options\EntityManager */
        $options 	= $this->getOptions($sl, 'entitymanager');
        $connection = $sl->get($options->getConnection());
        $config     = $sl->get($options->getConfiguration());

        return EntityManagerMock::create($connection, $config);
    }

    public function getOptionsClass()
    {
        return 'DoctrineORMModule\Options\EntityManager';
    }
}

class EntityManagerMock extends EntityManager
{

    public static function createMock(ServiceLocatorInterface $sl)
    {
        $factory = new EntityManagerMockFactory();
        $em = $factory->createService($sl);
        $metadatas = $em->getMetadataFactory()->getAllMetadata();

        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($metadatas);

        return $em;
    }

}
