<?php

namespace eCamp\Core;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use eCampApp;
use eCamp\Lib\ISetup;
use Zend\Stdlib\Glob;

class Setup implements ISetup
{

    function GetCommands() {
        return ['prod-data', 'dev-data'];
    }

    function RunCommand($command) {
        switch ($command) {
            case 'prod-data':
                $this->loadProdData();
                break;

            case 'dev-data':
                $this->loadProdData();
                $this->loadDevData();
                break;
        }
    }

    private function loadProdData() {
        echo PHP_EOL;
        echo ' Load ProdData-Fixtures: ' . PHP_EOL;
        echo '-------------------------' . PHP_EOL;

        $loader = new Loader();

        $paths = Glob::glob(__DIR__ . "/../data/prod/*.php");
        foreach ($paths as $path) {
            echo $path . PHP_EOL;
            $loader->loadFromFile($path);
        }

        $this->loadData($loader);

        echo PHP_EOL;
    }

    private function loadDevData() {
        echo PHP_EOL;
        echo ' Load DevData-Fixtures: ' . PHP_EOL;
        echo '------------------------' . PHP_EOL;

        $loader = new Loader();

        $paths = Glob::glob(__DIR__ . "/../data/dev/*.php");
        foreach ($paths as $path) {
            echo $path . PHP_EOL;
            $loader->loadFromFile($path);
        }

        $this->loadData($loader);

        echo PHP_EOL;
    }

    private function loadData(Loader $loader) {
        $em = eCampApp::GetEntityManager();

        $executor = new ORMExecutor($em, new ORMPurger());
        $executor->execute($loader->getFixtures(), true);
    }

}
