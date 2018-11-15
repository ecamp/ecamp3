<?php

namespace eCamp\Lib\Util;

use Zend\Uri\UriFactory;

class UrlUtils {

    public static function addQueryParameterToUrl($url, $paramName, $paramValue) {
        $uri = UriFactory::factory($url);
        $query = $uri->getQueryAsArray();
        $query[$paramName] = $paramValue;
        $uri->setQuery($query);
        return $uri->toString();
    }

}
