<?php

namespace eCamp\LibTest\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Tools\SchemaTool;
use eCamp\Lib\Command\RebuildDatabaseSchemaCommand;
use eCamp\LibTest\PHPUnit\AbstractConsoleControllerTestCase;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\Constraint\StringEndsWith;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @internal
 */
class RebuildDatabaseSchemaCommandTest extends AbstractConsoleControllerTestCase {
    public function testRebuildsDatabaseSchema() {
        // given
        $services = $this->getApplicationServiceLocator();

        $mockMetadata = [];
        $mockMetadataFactory = $this->createMock(ClassMetadataFactory::class);
        $mockMetadataFactory->method('getAllMetadata')->willReturn($mockMetadata);
        $mockEntityManager = $this->createMock(EntityManager::class);
        $mockEntityManager->method('getMetadataFactory')->willReturn($mockMetadataFactory);
        $services->setService(EntityManager::class, $mockEntityManager);

        $mockSchemaTool = $this->createMock(SchemaTool::class);
        $services->setService(SchemaTool::class, $mockSchemaTool);

        $mockFilesystem = $this->createMock(Filesystem::class);
        $services->setService(Filesystem::class, $mockFilesystem);

        /** @var RebuildDatabaseSchemaCommand $command */
        $command = $services->get(RebuildDatabaseSchemaCommand::class);

        // then
        $mockSchemaTool->expects($this->once())->method('dropDatabase')->willReturn(null);
        $mockSchemaTool->expects($this->once())->method('createSchema')->with($mockMetadata)->willReturn(null);

        // when
        $result = $this->runCommand($command);

        // then
        $this->assertThat($result, new IsEqual(RebuildDatabaseSchemaCommand::SUCCESS));
    }

    public function testCleansUpDoctrineProxies() {
        // given
        $services = $this->getApplicationServiceLocator();

        $mockMetadata = [];
        $mockMetadataFactory = $this->createMock(ClassMetadataFactory::class);
        $mockMetadataFactory->method('getAllMetadata')->willReturn($mockMetadata);
        $mockEntityManager = $this->createMock(EntityManager::class);
        $mockEntityManager->method('getMetadataFactory')->willReturn($mockMetadataFactory);
        $services->setService(EntityManager::class, $mockEntityManager);

        $mockSchemaTool = $this->createMock(SchemaTool::class);
        $services->setService(SchemaTool::class, $mockSchemaTool);

        $mockFilesystem = $this->createMock(Filesystem::class);
        $services->setService(Filesystem::class, $mockFilesystem);

        /** @var RebuildDatabaseSchemaCommand $command */
        $command = $services->get(RebuildDatabaseSchemaCommand::class);

        // then
        $mockFilesystem->expects($this->once())->method('remove')->with(new StringEndsWith('DoctrineORMModule'));

        // when
        $this->runCommand($command);
    }
}
