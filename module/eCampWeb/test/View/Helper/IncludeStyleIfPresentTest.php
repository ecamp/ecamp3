<?php

namespace eCamp\WebTest;

use eCamp\Web\View\Helper\IncludeStyleIfPresent;
use Zend\Test\PHPUnit\Controller\AbstractControllerTestCase;
use Zend\View\Exception\InvalidArgumentException;
use Zend\View\Helper\Asset;

class IncludeStyleIfPresentTest extends AbstractControllerTestCase {
    private $mockedAssetName = 'mocked-asset.css';
    private $mockedAssetURL = 'mocked-asset-url';
    private $mockedAlternativeText = 'mocked alternative text';

    private $assetViewHelperMock;

    private $viewHelper;

    public function setUp() {
        $config = include '../../../../../config/application.config.php';
        $bootstrap = \Zend\Mvc\Application::init($config);
        $serviceManager = $bootstrap->getServiceManager();
        $vhm = $serviceManager->get('ViewHelperManager');

        $this->assetViewHelperMock = $this->getMockBuilder(Asset::class)->disableOriginalConstructor()->getMock();
        $vhm->setFactory(Asset::class, function() {
            return $this->assetViewHelperMock;
        });

        $this->viewHelper = new IncludeStyleIfPresent();
        $this->viewHelper->setView($vhm->getRenderer());

        parent::setUp();
    }

    public function testIncludeScript() {

        // given
        $this->assetViewHelperMock->expects($this->any())->method('__invoke')->willReturn($this->mockedAssetURL);

        // when
        $rendered = $this->viewHelper->__invoke($this->mockedAssetName, $this->mockedAlternativeText);

        // then
        $this->assertEquals('<link rel="stylesheet" type="text/css" href="' . $this->mockedAssetURL . '" />', $rendered);
    }

    public function testOutputAlternativeText() {

        // given
        $this->assetViewHelperMock->expects($this->any())->method('__invoke')->willThrowException(new InvalidArgumentException());

        // when
        $rendered = $this->viewHelper->__invoke('asset-name.js', $this->mockedAlternativeText);

        // then
        $this->assertEquals($this->mockedAlternativeText, $rendered);
    }
}
