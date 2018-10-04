<?php

namespace eCamp\WebTest\View\Helper;

use eCamp\Web\View\Helper\IncludeStyleIfPresent;
use Zend\View\Exception\InvalidArgumentException;

class IncludeStyleIfPresentTest extends AbstractAssetViewHelperTest {
    protected function createViewHelperInstance() {
        return new IncludeStyleIfPresent();
    }

    public function testIncludeScript() {

        // given
        $this->assetViewHelperMock->expects(self::once())->method('__invoke')->willReturn($this->mockedAssetURL);

        // when
        $rendered = $this->viewHelper->__invoke($this->mockedAssetName, $this->mockedAlternativeText);

        // then
        self::assertEquals('<link rel="stylesheet" type="text/css" href="' . $this->mockedAssetURL . '" />', $rendered);
    }

    public function testOutputAlternativeText() {

        // given
        $this->assetViewHelperMock->expects(self::once())->method('__invoke')->willThrowException(new InvalidArgumentException());

        // when
        $rendered = $this->viewHelper->__invoke('asset-name.js', $this->mockedAlternativeText);

        // then
        self::assertEquals($this->mockedAlternativeText, $rendered);
    }
}
