<?php

namespace eCamp\LibTest\PHPUnit;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use Laminas\Cli\ApplicationFactory;
use Laminas\Test\PHPUnit\Controller\AbstractConsoleControllerTestCase as LaminasAbstractConsoleControllerTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractConsoleControllerTestCase extends LaminasAbstractConsoleControllerTestCase {
    /**
     * Host name in URIs returned by API.
     *
     * @var string
     */
    protected $host = '';

    protected $symfonyApplication;

    /**
     * @throws ToolsException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function setUp(): void {
        parent::setUp();

        $data = include __DIR__.'/../../../../config/application.config.php';
        $this->setApplicationConfig($data);

        $em = $this->getEntityManager();
        $this->createDatabaseSchema($em);
    }

    public function runCommand(Command $command, InputInterface $input = null, OutputInterface $output = null): void {
        $command->setApplication($this->getSymfonyApplication());
        $command->run(
            $input ?? new StringInput(''),
            $output ?? new ConsoleOutput()
        );
    }

    public function getSymfonyApplication(): Application {
        if (!$this->symfonyApplication) {
            $this->symfonyApplication = (new ApplicationFactory())($this->getApplicationServiceLocator());
        }

        return $this->symfonyApplication;
    }

    protected function getEntityManager(?string $name = null): EntityManager {
        $name = $name ?: 'orm_default';
        $name = 'doctrine.entitymanager.'.$name;

        return $this->getApplicationServiceLocator()->get($name);
    }

    /** @throws ToolsException */
    protected function createDatabaseSchema(EntityManager $em): void {
        $metadatas = $em->getMetadataFactory()->getAllMetadata();

        $schemaTool = new SchemaTool($em);
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($metadatas);
    }

    /**
     * loads data from Fixtures into ORM.
     */
    protected function loadFixtures(Loader $loader): void {
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->getEntityManager(), $purger);
        $executor->execute($loader->getFixtures());
    }
}
