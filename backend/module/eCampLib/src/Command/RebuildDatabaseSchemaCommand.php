<?php

namespace eCamp\Lib\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class RebuildDatabaseSchemaCommand extends Command {
    private $entityManager;
    private $schemaTool;
    private $filesystem;

    public function __construct(EntityManager $entityManager, SchemaTool $schemaTool, Filesystem $filesystem) {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->schemaTool = $schemaTool;
        $this->filesystem = $filesystem;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $allMetadata = $this->entityManager->getMetadataFactory()->getAllMetadata();

        $this->schemaTool->dropDatabase();
        $this->schemaTool->createSchema($allMetadata);

        // Cleaning up the generated Doctrine proxies is necessary because the command might be run by a user other
        // than www-data.
        $this->filesystem->remove(__DIR__.'/../../../../data/DoctrineORMModule');

        return 0;
    }
}
