<?php

namespace eCamp\Web\Route;

use Zend\Router\RouteMatch;

class GroupRouter extends FluentRouter {
    /**
     * Group-Router erlaubt kein User-Match
     *
     * @param $path
     * @param $length
     * @param array $params
     * @return null|RouteMatch
     */
    protected function matchUser($path, $length, $params) {
        return null;
    }

    /**
     * Group-Router erlaubt keine Camp-Match
     *
     * @param $path
     * @param $length
     * @param $params
     * @return null|RouteMatch
     */
    protected function matchCamp($path, $length, $params) {
        return null;
    }
}
