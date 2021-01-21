<?php

namespace eCamp\Lib\Command;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class UpdateDatabaseSchemaCommand extends Command {
    private $entityManager;
    private $schemaTool;
    private $filesystem;

    public function __construct(EntityManager $entityManager, SchemaTool $schemaTool) {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->schemaTool = $schemaTool;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $allMetadata = $this->entityManager->getMetadataFactory()->getAllMetadata();

        $this->schemaTool->updateSchema($allMetadata);

        return 0;
    }
}
