<?php

namespace eCamp\Lib\Hal;

use eCamp\Lib\Router\Http\TplParameter;
use ZF\Hal\Link\Link as HalLink;

class Link extends HalLink {
    public static function tplParam($value) {
        return new TplParameter($value);
    }

    public function isTemplated() {
        foreach ($this->getRouteParams() as $routeParam) {
            if ($routeParam instanceof TplParameter) {
                return true;
            }
        }
        return false;
    }
}
