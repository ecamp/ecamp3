<?php

namespace eCamp\Web\View\Helper;

use Zend\View\Exception\InvalidArgumentException;
use Zend\View\Helper\AbstractHelper;

class IncludeScriptIfPresent extends AbstractHelper {
    public function __invoke($assetName, $alternativeText = '') {
        try {
            return '<script type="text/javascript" src="' . $this->getView()->asset($assetName) . '"></script>';
        } catch (InvalidArgumentException $e) {
            return $alternativeText;
        }
    }
}
