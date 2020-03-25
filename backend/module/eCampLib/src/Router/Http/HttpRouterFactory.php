<?php


namespace eCamp\Lib\Router\Http;

use Interop\Container\ContainerInterface;
use Zend\Router\RouterConfigTrait;
use Zend\Router\RouteStackInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class HttpRouterFactory implements FactoryInterface {
    use RouterConfigTrait;

    /**
     * Create and return the HTTP router
     *
     * Retrieves the "router" key of the Config service, and uses it
     * to instantiate the router. Uses the TreeRouteStack implementation by
     * default.
     *
     * @param  ContainerInterface $container
     * @param  string $name
     * @param  null|array $options
     * @return RouteStackInterface
     */
    public function __invoke(ContainerInterface $container, $name, array $options = null) {
        $config = $container->has('config') ? $container->get('config') : [];

        // Defaults
        $class  = TreeRouteStack::class;
        $config = isset($config['router']) ? $config['router'] : [];

        return $this->createRouter($class, $config, $container);
    }

    /**
     * Create and return RouteStackInterface instance
     *
     * For use with zend-servicemanager v2; proxies to __invoke().
     *
     * @param ServiceLocatorInterface $container
     * @return RouteStackInterface
     */
    public function createService(ServiceLocatorInterface $container) {
        return $this($container, RouteStackInterface::class);
    }
}
