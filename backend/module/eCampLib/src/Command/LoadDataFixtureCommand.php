<?php

namespace eCamp\Lib\Command;

use Doctrine\ORM\EntityManager;
use eCamp\Lib\Fixture\FixtureLoader;
use Laminas\Cli\Command\AbstractParamAwareCommand;
use Laminas\Cli\Input\PathParam;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoadDataFixtureCommand extends AbstractParamAwareCommand {
    private $entityManager;
    private $fixtureLoader;

    public function __construct(EntityManager $entityManager, FixtureLoader $fixtureLoader) {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->fixtureLoader = $fixtureLoader;
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

        return 0;
    }
}
