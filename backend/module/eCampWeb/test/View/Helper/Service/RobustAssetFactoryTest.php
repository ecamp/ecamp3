<?php

namespace eCamp\WebTest\View\Helper;

use eCamp\LibTest\PHPUnit\AbstractTestCase;
use eCamp\Web\View\Helper\Service\RobustAssetFactory;
use Zend\ServiceManager\ServiceManager;

class RobustAssetFactoryTest extends AbstractTestCase {
    private $containerMock;
    private $robustAssetFactory;

    public function setUp() {
        $this->containerMock = $this->createMock(ServiceManager::class);
        $this->robustAssetFactory = new RobustAssetFactory();

        parent::setUp();
    }

    public function testCreateWhenAssetsFileCanBeRead() {
        // given
        $resourceMap = [ 'test' => 'abc' ];
        $this->containerMock->expects(self::once())->method('get')->willReturn(['view_helper_config' => ['asset' => [ 'resource_map' => $resourceMap ] ] ]);

        // when
        $asset = $this->robustAssetFactory->__invoke($this->containerMock, 'dummy name');

        // then
        self::assertEquals($resourceMap, $asset->getResourceMap());
    }

    public function testCreateWhenAssetsIsNull() {
        // given
        $resourceMap = null;
        $this->containerMock->expects(self::once())->method('get')->willReturn(['view_helper_config' => ['asset' => [ 'resource_map' => $resourceMap ] ] ]);

        // when
        $asset = $this->robustAssetFactory->__invoke($this->containerMock, 'dummy name');

        // then
        self::assertEmpty($asset->getResourceMap());
    }

    public function testCreateWhenAssetsFileCannotBeRead() {
        // given
        $resourceMap = [];
        $this->containerMock->expects(self::once())->method('get')->willReturn(['view_helper_config' => ['asset' => [ 'resource_map' => $resourceMap ] ] ]);

        // when
        $asset = $this->robustAssetFactory->__invoke($this->containerMock, 'dummy name');

        // then
        self::assertEmpty($asset->getResourceMap());
    }
}
