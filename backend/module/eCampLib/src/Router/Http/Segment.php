<?php

namespace eCamp\Lib\Router\Http;

use Zend\Router\Http\Segment as ZendSegment;

class Segment extends ZendSegment {
    function encode($value) {
        if ($value instanceof TplParameter) {
            // TplParameter will not bet encoded.
            return (string) $value;
        }

        return  parent::encode($value);
    }
}