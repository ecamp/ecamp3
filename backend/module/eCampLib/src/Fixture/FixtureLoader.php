<?php

namespace eCamp\Lib\Fixture;

use Zend\ServiceManager\ServiceManager;
use Interop\Container\ContainerInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\Loader as BaseLoader;


class FixtureLoader extends BaseLoader {
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ServiceManager $container) {
        $this->container = $container;
    }

    public function addFixture(FixtureInterface $fixture) {
        if ($fixture instanceof ContainerAwareInterface) {
            $fixture->setContainer($this->container);
        }
        parent::addFixture($fixture);
    }
}