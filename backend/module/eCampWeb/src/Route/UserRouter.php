<?php

namespace eCamp\Web\Route;

use Zend\Router\RouteMatch;

class UserRouter extends FluentRouter {
    /**
     * User-Router erlaubt kein Group-Match
     *
     * @param $path
     * @param $length
     * @param $params
     * @return null|RouteMatch
     */
    protected function matchGroup($path, $length, $params) {
        return null;
    }

    /**
     * User-Router erlaubt keine Camp-Match
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
