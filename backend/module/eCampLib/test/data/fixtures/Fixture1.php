<?php

namespace eCamp\LibTest\data\fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\ActivityCategory;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\ContentType;
use eCamp\Core\Entity\ContentTypeConfig;
use Symfony\Component\Console\Output\OutputInterface;

class Fixture1 extends AbstractFixture {

    private $output;

    public function __construct(OutputInterface $output) {
        $this->output = $output;
    }

    public function load(ObjectManager $manager) {
        $this->output->writeln('Fixture1');
    }
}
