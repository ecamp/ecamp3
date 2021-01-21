<?php

namespace eCamp\Lib\Command;

use Doctrine\ORM\EntityManager;
use eCamp\Lib\Fixture\FixtureLoader;
use Laminas\Cli\Command\AbstractParamAwareCommand;
use Laminas\Cli\Input\PathParam;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class LoadDataFixturesCommand extends AbstractParamAwareCommand {
    private $entityManager;
    private $fixtureLoader;
    private $filesystem;

    public function __construct(EntityManager $entityManager, FixtureLoader $fixtureLoader, Filesystem $filesystem) {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->fixtureLoader = $fixtureLoader;
        $this->filesystem = $filesystem;
    }

    protected function configure() {
        $this->addParam(new PathParam('path', PathParam::TYPE_DIR));
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $path = $input->getParam('path');

        $files = \Laminas\Stdlib\Glob::glob($path.'/*.php');
        foreach ($files as $file) {
            $this->fixtureLoader->loadFromFile($file);
        }

        $executor = new \Doctrine\Common\DataFixtures\Executor\ORMExecutor(
            $this->entityManager,
            new \Doctrine\Common\DataFixtures\Purger\ORMPurger()
        );
        $executor->execute($this->fixtureLoader->getFixtures(), true);

        // Cleaning up the generated Doctrine proxies is necessary because the command might be run by a user other
        // than www-data.
        $this->filesystem->remove(__DIR__.'/../../../../data/DoctrineORMModule');

        return 0;
    }
}
