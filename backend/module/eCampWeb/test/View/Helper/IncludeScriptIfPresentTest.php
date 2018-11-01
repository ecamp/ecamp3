<?php

namespace eCamp\WebTest\View\Helper;

use eCamp\Web\View\Helper\IncludeScriptIfPresent;
use Zend\View\Exception\InvalidArgumentException;

class IncludeScriptIfPresentTest extends AbstractAssetViewHelperTest {
    protected function createViewHelperInstance() {
        return new IncludeScriptIfPresent();
    }

    public function testIncludeScript() {

        // given
        $this->assetViewHelperMock->expects(self::once())->method('__invoke')->willReturn($this->mockedAssetURL);

        // when
        $rendered = $this->viewHelper->__invoke($this->mockedAssetName, $this->mockedAlternativeText);

        // then
        self::assertEquals('<script type="text/javascript" src="' . $this->mockedAssetURL . '"></script>', $rendered);
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
