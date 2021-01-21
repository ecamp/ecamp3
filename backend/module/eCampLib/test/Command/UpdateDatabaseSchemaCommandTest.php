<?php

namespace eCamp\LibTest\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Doctrine\ORM\Tools\SchemaTool;
use eCamp\Lib\Command\UpdateDatabaseSchemaCommand;
use eCamp\LibTest\PHPUnit\AbstractConsoleControllerTestCase;
use PHPUnit\Framework\Constraint\IsEqual;
use PHPUnit\Framework\Constraint\StringEndsWith;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @internal
 */
class UpdateDatabaseSchemaCommandTest extends AbstractConsoleControllerTestCase {
    public function testUpdatesDatabaseSchema() {
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

        /** @var UpdateDatabaseSchemaCommand $command */
        $command = $services->get(UpdateDatabaseSchemaCommand::class);

        // then
        $mockSchemaTool->expects($this->once())->method('updateSchema')->with($mockMetadata)->willReturn(null);

        // when
        $result = $this->runCommand($command);

        // then
        $this->assertThat($result, new IsEqual(UpdateDatabaseSchemaCommand::SUCCESS));
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

        /** @var UpdateDatabaseSchemaCommand $command */
        $command = $services->get(UpdateDatabaseSchemaCommand::class);

        // then
        $mockFilesystem->expects($this->once())->method('remove')->with(new StringEndsWith('DoctrineORMModule'));

        // when
        $this->runCommand($command);
    }
}
