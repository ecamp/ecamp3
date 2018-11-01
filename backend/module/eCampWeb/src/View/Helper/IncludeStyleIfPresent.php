<?php

namespace eCamp\Web\View\Helper;

use Zend\View\Exception\InvalidArgumentException;
use Zend\View\Helper\AbstractHelper;

class IncludeStyleIfPresent extends AbstractHelper {
    public function __invoke($assetName, $alternativeText = '') {
        try {
            return '<link rel="stylesheet" type="text/css" href="' . $this->getView()->asset($assetName) . '" />';
        } catch (InvalidArgumentException $e) {
            return $alternativeText;
        }
    }
}
