<?php


namespace eCamp\Lib\Router\Http;

use Zend\Router\Http\TreeRouteStack as ZendTreeRouteStack;

class TreeRouteStack extends ZendTreeRouteStack {
    protected function init() {
        parent::init();

        $this->routePluginManager->setAlias('segment', Segment::class);
        $this->routePluginManager->setAlias('Segment', Segment::class);
    }
}