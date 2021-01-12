<?php

namespace eCamp\Lib\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateDatabaseSchemaCommand extends Command {
    private $entityManager;

    public function __construct(EntityManager $entityManager) {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->entityManager);
        $allMetadata = $this->entityManager->getMetadataFactory()->getAllMetadata();

        $schemaTool->updateSchema($allMetadata);

        return 0;
    }
}
