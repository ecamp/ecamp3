<?php

namespace eCamp\WebTest\View\Helper;

use eCamp\LibTest\PHPUnit\AbstractTestCase;
use Zend\View\Helper\Asset;

abstract class AbstractAssetViewHelperTest extends AbstractTestCase {
    protected $mockedAssetName = 'mocked-asset.js';
    protected $mockedAssetURL = 'mocked-asset-url';
    protected $mockedAlternativeText = 'mocked alternative text';

    protected $assetViewHelperMock;

    protected $viewHelper;

    public function setUp() {
        $config = include 'config/application.config.php';
        $bootstrap = \Zend\Mvc\Application::init($config);
        $serviceManager = $bootstrap->getServiceManager();
        $vhm = $serviceManager->get('ViewHelperManager');

        $this->assetViewHelperMock = $this->createMock(Asset::class);
        $vhm->setFactory(Asset::class, function() {
            return $this->assetViewHelperMock;
        });

        $this->viewHelper = $this->createViewHelperInstance();
        $this->viewHelper->setView($vhm->getRenderer());

        parent::setUp();
    }

    protected abstract function createViewHelperInstance();
}
