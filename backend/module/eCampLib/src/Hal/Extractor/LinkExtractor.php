<?php

namespace eCamp\Lib\Hal\Extractor;

use eCamp\Lib\Hal\TemplatedLink;
use Laminas\ApiTools\ApiProblem\Exception\DomainException;
use Laminas\ApiTools\Hal\Extractor\LinkExtractor as HalLinkExtractor;
use Laminas\ApiTools\Hal\Link\Link;
use Laminas\ApiTools\Hal\Link\LinkUrlBuilder;
use Laminas\Mvc\ModuleRouteListener;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Part;
use Laminas\Router\Http\RouteInterface;
use Laminas\Router\Http\Segment;
use Laminas\Router\RouteMatch;
use Laminas\Router\SimpleRouteStack;
use Laminas\View\Helper\ServerUrl;
use Laminas\View\Helper\Url;

class LinkExtractor extends HalLinkExtractor {
    protected SimpleRouteStack $router;
    protected ?RouteMatch $routeMatch;
    protected ServerUrl $serverUrlHelper;
    protected Url $urlHelper;
    protected array $zfRestConfig;
    protected array $rpcConfig;

    public function __construct(LinkUrlBuilder $linkUrlBuilder) {
        parent::__construct($linkUrlBuilder);

        $this->routeMatch = null;
    }

    public function getRouter(): SimpleRouteStack {
        return $this->router;
    }

    public function setRouter(SimpleRouteStack $router): self {
        $this->router = $router;

        return $this;
    }

    public function getRouteMatch(): RouteMatch {
        return $this->routeMatch;
    }

    public function setRouteMatch(RouteMatch $routeMatch = null): self {
        $this->routeMatch = $routeMatch;

        return $this;
    }

    public function getServerUrl(): ServerUrl {
        return $this->serverUrlHelper;
    }

    public function setServerUrl(ServerUrl $serverUrlHelper): self {
        $this->serverUrlHelper = $serverUrlHelper;

        return $this;
    }

    public function getUrl(): Url {
        return $this->urlHelper;
    }

    public function setUrl(Url $urlHelper): self {
        $this->urlHelper = $urlHelper;

        return $this;
    }

    public function getZfRestConfig(): array {
        return $this->zfRestConfig;
    }

    public function setZfRestConfig(array $zfRestConfig): self {
        $this->zfRestConfig = $zfRestConfig;

        return $this;
    }

    public function getRpcConfig(): array {
        return $this->rpcConfig;
    }

    public function setRpcConfig(array $rpcConfig): self {
        $this->rpcConfig = $rpcConfig;

        return $this;
    }

    /**
     * @throws \ReflectionException
     */
    public function extract(Link $object): array {
        if ($object instanceof TemplatedLink) {
            return $this->extractTemplatedLink($object);
        }

        return parent::extract($object);
    }

    /**
     * @throws \ReflectionException
     */
    public function extractTemplatedLink(TemplatedLink $object): array {
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
     */
    public function buildLinkUrl($name, $params = [], $options = [], $reUseMatchedParams = false): string {
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
     */
    protected function assemble(SimpleRouteStack $router, string $name, array $params, array $options, $uri = ''): string {
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
     */
    protected function assembleLiteral(Literal $route): string {
        $reflectionProp = new \ReflectionProperty(get_class($route), 'route');
        $reflectionProp->setAccessible(true);

        return $reflectionProp->getValue($route);
    }

    /**
     * Get a URI template for a segment route.
     *
     * @throws \ReflectionException
     */
    protected function assembleSegment(Segment $segment, array $params = [], array $options = []): string {
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
     * @param $isOptional
     * @param $hasChild
     */
    protected function buildPathSegment($parts, array $defaults, array $params, $isOptional, $hasChild, array $options): string {
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
     */
    protected function extractRouteFromPartRoute(Part $route): RouteInterface {
        $reflectionProp = new \ReflectionProperty(get_class($route), 'route');
        $reflectionProp->setAccessible(true);

        return $reflectionProp->getValue($route);
    }

    /**
     * Add a Part-routes childroutes as routes.
     *
     * @throws \ReflectionException
     */
    protected function addChildRoutes(Part $route): void {
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
     */
    protected function getZfRestQueryParamsAsUriTemplate(RouteInterface $childRouteRealRoute): string {
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
        $rpcConfig = $this->getRpcConfig();
        $controller = $defaults['controller'];

        $config = [];
        if (isset($zfRestConfig[$controller])) {
            $config = $zfRestConfig[$controller];
        }
        if (isset($rpcConfig[$controller])) {
            $config = $rpcConfig[$controller];
        }
        if (!isset($config['collection_query_whitelist'])) {
            return '';
        }
        $queryWhiteList = $config['collection_query_whitelist'];

        return sprintf('{?%s}', implode(',', $queryWhiteList));
    }
}
