<?php

namespace eCamp\Lib\Fixture;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\Loader as BaseLoader;
use Interop\Container\ContainerInterface;

class FixtureLoader extends BaseLoader {
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function addFixture(FixtureInterface $fixture) {
        if ($fixture instanceof ContainerAwareInterface) {
            $fixture->setContainer($this->container);
        }
        parent::addFixture($fixture);
    }

    /**
     * Creates the fixture object from the class.
     *
     * @param string $class
     */
    protected function createFixture($class): FixtureInterface {
        return $this->container->get($class);
    }
}
