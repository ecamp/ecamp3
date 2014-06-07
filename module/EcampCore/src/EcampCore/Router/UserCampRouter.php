<?php

namespace EcampCore\Router;

use Zend\Mvc\Router\Http\RouteMatch;

use Zend\Mvc\Router\Exception;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Mvc\Router\Http\RouteInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class UserCampRouter
    implements RouteInterface
    , ServiceLocatorAwareInterface
{

    private $serviceLocator;

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function getUserRepository()
    {
        return $this->getServiceLocator()->getServiceLocator()->get('EcampCore\Repository\User');
    }

    public function getCampRepository()
    {
        return $this->getServiceLocator()->getServiceLocator()->get('EcampCore\Repository\Camp');
    }

    /**
     * Default values.
     * @var array
     */
    protected $defaults;

    public function __construct(array $defaults = array())
    {
        $this->defaults = $defaults;
    }

    public static function factory($options = array())
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } elseif (!is_array($options)) {
            throw new Exception\InvalidArgumentException(__METHOD__ . ' expects an array or Traversable set of options');
        }

        if (!isset($options['defaults'])) {
            $options['defaults'] = array();
        }

        return new static(
            $options['defaults']
        );
    }

    /**
     * match(): defined by RouteInterface interface.
     *
     * @see    \Zend\Mvc\Router\RouteInterface::match()
     * @param  Request         $request
     * @param  int|null        $pathOffset
     * @return RouteMatch|null
     */
    public function match(Request $request, $pathOffset = null)
    {
        if (!method_exists($request, 'getUri')) {
            return null;
        }

        $uri  = $request->getUri();
        $path = $uri->getPath();

        if ($pathOffset !== null) {
            if ($pathOffset >= 0 && strlen($path) >= $pathOffset) {
                $traceInfo = $this->traceCamp(substr($path, $pathOffset));
            } else {
                return null;
            }
        } else {
            $traceInfo = $this->traceCamp($path);
        }

        $user   = $traceInfo['user'];
        $camp   = $traceInfo['camp'];
        $length = $traceInfo['length'];

        if ($user == null || $camp == null) {
            return null;
        } else {
            $userId = $user->getId();
            $campId  = ($camp != null) ? $camp->getId() : null;

            $params = array_merge(array('user' => $userId, 'camp' => $campId), $this->defaults);

            return new RouteMatch($params, $length);
        }
    }

    private function traceCamp($path)
    {
        $trim_path = ltrim($path, '/');
        $length = strlen($path) - strlen($trim_path);

        $names = explode("/", $trim_path);

        if (count($names)) {
            $userName = array_shift($names);
        }

        if (count($names)) {
            $campName = array_shift($names);
        }

        $user = $this->getUserRepository()->findOneBy(
            array('username' => urldecode($userName)));

        if ($user != null) {
            $length += strlen($userName);

            $camp = $this->getCampRepository()->findOneBy(
                array('name' => urldecode($campName), 'owner' => $user));

            if ($camp != null) {
                $length += (strlen($campName) + 1);
            }
        } else {
            return null;
        }

        return array('user' => $user, 'camp' => $camp, 'length' => $length);
    }

    /**
     * assemble(): Defined by RouteInterface interface.
     *
     * @see    \Zend\Mvc\Router\RouteInterface::assemble()
     * @param  array $params
     * @param  array $options
     * @return mixed
     */
    public function assemble(array $params = array(), array $options = array())
    {
        $userId = $params['user'];
        $user = $this->getUserRepository()->find($userId);

        $campId = $params['camp'];
        $camp = ($campId != null) ? $this->getCampRepository()->find($campId) : null;

        $path = "/" . urlencode($user->getUsername());

        if ($camp) {
            $path .= "/" . urlencode($camp->getName());
        }

        return $path;
    }

    public function getAssembledParams()
    {
        return array();
    }
}
