<?php

namespace eCamp\Lib\Hal\Extractor;

use eCamp\Lib\Hal\TemplatedLink;
use Zend\Mvc\ModuleRouteListener;
use Zend\Router\Http\Literal;
use Zend\Router\Http\Part;
use Zend\Router\Http\RouteInterface;
use Zend\Router\Http\Segment;
use Zend\Router\RouteMatch;
use Zend\Router\SimpleRouteStack;
use Zend\View\Helper\ServerUrl;
use Zend\View\Helper\Url;
use ZF\ApiProblem\Exception\DomainException;
use ZF\Hal\Extractor\LinkExtractor as HalLinkExtractor;
use ZF\Hal\Link\Link;

class LinkExtractor extends HalLinkExtractor {
    /** @var SimpleRouteStack */
    protected $router;

    /** @var RouteMatch */
    protected $routeMatch;

    /** @var ServerUrl */
    protected $serverUrlHelper;

    /** @var Url */
    protected $urlHelper;

    /** @var array */
    protected $zfRestConfig;

    /** @return SimpleRouteStack */
    public function getRouter() {
        return $this->router;
    }

    /**
     * @return $this
     */
    public function setRouter(SimpleRouteStack $router) {
        $this->router = $router;

        return $this;
    }

    /** @return RouteMatch */
    public function getRouteMatch() {
        return $this->routeMatch;
    }

    /**
     * @param RouteMatch $routeMatch
     *
     * @return $this
     */
    public function setRouteMatch(RouteMatch $routeMatch = null) {
        $this->routeMatch = $routeMatch;

        return $this;
    }

    /** @return ServerUrl */
    public function getServerUrl() {
        return $this->serverUrlHelper;
    }

    /**
     * @return $this
     */
    public function setServerUrl(ServerUrl $serverUrlHelper) {
        $this->serverUrlHelper = $serverUrlHelper;

        return $this;
    }

    /** @return Url */
    public function getUrl() {
        return $this->urlHelper;
    }

    /**
     * @return $this
     */
    public function setUrl(Url $urlHelper) {
        $this->urlHelper = $urlHelper;

        return $this;
    }

    /** @return array */
    public function getZfRestConfig() {
        return $this->zfRestConfig;
    }

    /**
     * @param array $zfRestConfig
     *
     * @return $this
     */
    public function setZfRestConfig($zfRestConfig) {
        $this->zfRestConfig = $zfRestConfig;

        return $this;
    }

    /**
     * @throws \ReflectionException
     *
     * @return array
     */
    public function extract(Link $object) {
        if ($object instanceof TemplatedLink) {
            return $this->extractTemplatedLink($object);
        }

        return parent::extract($object);
    }

    /**
     * @throws \ReflectionException
     *
     * @return array
     */
    public function extractTemplatedLink(TemplatedLink $object) {
        if (!$object->isComplete()) {
            throw new DomainException(sprintf(
                'Link from resource provided to %s was incomplete; must contain a URL or a route',
                __METHOD__
            ));
        }

        $reuseMatchedParams = true;
        $options = $object->getRouteOptions();
        if (isset($options['reuse_matched_params'])) {
            $reuseMatchedParams = (bool) $options['reuse_matched_params'];
            unset($options['reuse_matched_params']);
        }

        $representation = $object->getAttributes();
        $representation['href'] = $this->buildLinkUrl(
            $object->getRoute(),
            $object->getRouteParams(),
            $options,
            $reuseMatchedParams
        );
        $representation['templated'] = true;

        return $representation;
    }

    /**
     * Get an URI Template from a ZF2 route.
     *
     * @param string $name
     * @param array  $params
     * @param array  $options
     * @param bool   $reUseMatchedParams
     *
     * @throws \ReflectionException
     *
     * @return string
     */
    public function buildLinkUrl($name, $params = [], $options = [], $reUseMatchedParams = false) {
        if ($reUseMatchedParams && null !== $this->routeMatch) {
            $routeMatchParams = $this->routeMatch->getParams();

            if (isset($routeMatchParams[ModuleRouteListener::ORIGINAL_CONTROLLER])) {
                $routeMatchParams['controller'] = $routeMatchParams[ModuleRouteListener::ORIGINAL_CONTROLLER];
                unset($routeMatchParams[ModuleRouteListener::ORIGINAL_CONTROLLER]);
            }

            if (isset($routeMatchParams[ModuleRouteListener::MODULE_NAMESPACE])) {
                unset($routeMatchParams[ModuleRouteListener::MODULE_NAMESPACE]);
            }

            $params = array_merge($routeMatchParams, $params);
        }

        $uri = $this->assemble($this->getRouter(), $name, $params, $options);

        return call_user_func($this->serverUrlHelper, $uri);
    }

    /**
     * @param string $uri
     *
     * @throws \ReflectionException
     *
     * @return string
     */
    protected function assemble(SimpleRouteStack $router, string $name, array $params, array $options, $uri = '') {
        $names = explode('/', $name, 2);
        $childRouteName = $names[0];

        if ($router instanceof Part) {
            $this->addChildRoutes($router);
        }
        $childRoute = $router->getRoute($childRouteName);

        if ($childRoute instanceof Part) {
            $childRouteRealRoute = $this->extractRouteFromPartRoute($childRoute);
        } else {
            $childRouteRealRoute = $childRoute;
        }

        if ($childRouteRealRoute instanceof Literal) {
            $uri .= $this->assembleLiteral($childRouteRealRoute);
        } elseif ($childRouteRealRoute instanceof Segment) {
            $uri .= $this->assembleSegment($childRouteRealRoute);
        }

        if (isset($names[1])) {
            // we have more children
            $uri = $this->assemble($childRoute, $names[1], $params, $options, $uri);
        } else {
            // we've hit the last route
            $uri .= $this->getZfRestQueryParamsAsUriTemplate($childRouteRealRoute);
        }

        return $uri;
    }

    /**
     * Get the URI template for a literal route.
     *
     * @throws \ReflectionException
     *
     * @return mixed
     */
    protected function assembleLiteral(Literal $route) {
        $reflectionProp = new \ReflectionProperty(get_class($route), 'route');
        $reflectionProp->setAccessible(true);

        return $reflectionProp->getValue($route);
    }

    /**
     * Get a URI template for a segment route.
     *
     * @throws \ReflectionException
     *
     * @return string
     */
    protected function assembleSegment(Segment $segment, array $params = [], array $options = []) {
        $reflectionProp = new \ReflectionProperty(get_class($segment), 'parts');
        $reflectionProp->setAccessible(true);
        $parts = $reflectionProp->getValue($segment);

        $reflectionProp = new \ReflectionProperty(get_class($segment), 'defaults');
        $reflectionProp->setAccessible(true);
        $defaults = $reflectionProp->getValue($segment);

        return $this->buildPathSegment(
            $parts,
            $defaults,
            $params,
            false,
            (isset($options['has_child']) ? $options['has_child'] : false),
            $options
        );
    }

    /**
     * @param $parts
     * @param array $mergedParams
     * @param $isOptional
     * @param $hasChild
     *
     * @return string
     */
    protected function buildPathSegment($parts, array $defaults, array $params, $isOptional, $hasChild, array $options) {
        $uri = '';
        $skip = true;
        $skippable = false;

        foreach ($parts as $part) {
            switch ($part[0]) {
                case 'literal':
                    $uri .= $part[1];

                    break;
                case 'parameter':
                    $skippable = true;
                    $skip = false;

                    $uri = rtrim($uri, '/');
                    $uri .= sprintf('{/%s}', $part[1]);

                    break;
                case 'optional':
                    $skippable = true;
                    $optionalPart = $this->buildPathSegment($part[1], $defaults, $params, true, $hasChild, $options);

                    if ('' !== $optionalPart) {
                        $uri .= $optionalPart;
                        $skip = false;
                    }

                    break;
                default:
                    throw new \InvalidArgumentException('Unsupported SegmentRoute-Part');
            }
        }

        if ($isOptional && $skippable && $skip) {
            return '';
        }

        return $uri;
    }

    /**
     * Get the route from a part route.
     *
     * @throws \ReflectionException
     *
     * @return mixed
     */
    protected function extractRouteFromPartRoute(Part $route) {
        $reflectionProp = new \ReflectionProperty(get_class($route), 'route');
        $reflectionProp->setAccessible(true);

        return $reflectionProp->getValue($route);
    }

    /**
     * Add a Part-routes childroutes as routes.
     *
     * @throws \ReflectionException
     */
    protected function addChildRoutes(Part $route) {
        $reflectionProp = new \ReflectionProperty(get_class($route), 'childRoutes');
        $reflectionProp->setAccessible(true);
        $childRoutes = $reflectionProp->getValue($route);

        if (is_array($childRoutes)) {
            $route->addRoutes($childRoutes);
            $reflectionProp->setValue($route, null);
        }
    }

    /**
     * Get ZF-Rest whitelisted queryparams for a route (based on the routes controller).
     *
     * @throws \ReflectionException
     *
     * @return string an URI template for queryparams
     */
    protected function getZfRestQueryParamsAsUriTemplate(RouteInterface $childRouteRealRoute) {
        $refObj = new \ReflectionObject($childRouteRealRoute);
        if (!$refObj->hasProperty('defaults')) {
            return '';
        }

        $refDefaultProperty = $refObj->getProperty('defaults');
        $refDefaultProperty->setAccessible(true);
        $defaults = $refDefaultProperty->getValue($childRouteRealRoute);

        if (!isset($defaults['controller'])) {
            return '';
        }

        $zfRestConfig = $this->getZfRestConfig();
        $controller = $defaults['controller'];

        if (!isset($zfRestConfig[$controller]['collection_query_whitelist'])) {
            return '';
        }
        $queryWhiteList = $zfRestConfig[$controller]['collection_query_whitelist'];

        return sprintf('{?%s}', implode(',', $queryWhiteList));
    }
}
