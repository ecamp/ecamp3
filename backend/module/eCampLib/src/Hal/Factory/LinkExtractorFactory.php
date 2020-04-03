<?php

namespace eCamp\Lib\Hal\Factory;

use eCamp\Lib\Hal\Extractor\LinkExtractor;
use Zend\Mvc\Application;
use Zend\Router\RouteMatch;
use Zend\View\Helper\ServerUrl;
use Zend\View\Helper\Url;
use ZF\Hal\Link\LinkUrlBuilder;

class LinkExtractorFactory {
    /**
     * @param  \Interop\Container\ContainerInterface|\Zend\ServiceManager\ServiceLocatorInterface $container
     * @return LinkExtractor
     */
    public function __invoke($container) {
        /** @var Application $application */
        $application = $container->get('Application');
        /** @var RouteMatch $routeMatch */
        $routeMatch = $application->getMvcEvent()->getRouteMatch();

        $viewHelperManager = $container->get('ViewHelperManager');
        /** @var ServerUrl $serverUrlHelper */
        $serverUrlHelper = $viewHelperManager->get('ServerUrl');
        /** @var Url $urlHelper */
        $urlHelper = $viewHelperManager->get('Url');

        $linkExtractor = new LinkExtractor($container->get(LinkUrlBuilder::class));
        $linkExtractor->setRouter($container->get('Router'));
        $linkExtractor->setRouteMatch($routeMatch);
        $linkExtractor->setServerUrl($serverUrlHelper);
        $linkExtractor->setUrl($urlHelper);


        if (isset($container->get('Config')['zf-rest'])) {
            $linkExtractor->setZfRestConfig($container->get('Config')['zf-rest']);
        }

        return $linkExtractor;
    }
}