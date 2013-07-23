<?php

namespace EcampCore\Router;

use Zend\Mvc\Router\Http\RouteMatch;

use Zend\Mvc\Router\Exception;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Mvc\Router\Http\RouteInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

class UserRouter
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
                $traceInfo = $this->traceUser(substr($path, $pathOffset));
            } else {
                return null;
            }
        } else {
            $traceInfo = $this->traceUser($path);
        }

        $user   = $traceInfo['user'];
        $length = $traceInfo['length'];

        if ($user == null) {
            return null;
        } else {
            $userId = $user->getId();

            $params = array_merge(array('user' => $userId), $this->defaults);

            return new RouteMatch($params, $length);
        }
    }

    private function traceUser($path)
    {
        $trim_path = ltrim($path, '/');
        $length = strlen($path) - strlen($trim_path);

        $names = explode("/", $trim_path);

        if (count($names)) {
            $userName = array_shift($names);
        }

        $user = $this->getUserRepository()->findOneBy(
            array('username' => $userName));

        if ($user != null) {
            $length += strlen($userName);
        } else {
            return null;
        }

        return array('user' => $user, 'length' => $length);
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

        $path = "/" . $user->getUsername();

        return $path;
    }

    public function getAssembledParams()
    {
        return array();
    }
}
