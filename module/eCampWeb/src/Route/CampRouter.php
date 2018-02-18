<?php

namespace eCamp\Web\Route;

class CampRouter extends FluentRouter
{
    protected function matchUser($path, $length, $params) {
        $match = parent::matchUser($path, $length, $params);

        if (!$match instanceof CampRouteMatch) {
            return null;
        }

        return $match;
    }

    protected function matchGroup($path, $length, $params) {
        $match = parent::matchGroup($path, $length, $params);

        if (!$match instanceof CampRouteMatch) {
            return null;
        }

        return $match;
    }
}
