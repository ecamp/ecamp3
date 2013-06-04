<?php

namespace EcampCore\Router;

use EcampCore\Repository\Provider\UserRepositoryProvider;

use Zend\Mvc\Router\Http\RouteMatch;

use EcampCore\DI\DependencyLocator;
use EcampCore\Repository\Provider\CampRepositoryProvider;

use Zend\Mvc\Router\Exception;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Mvc\Router\Http\RouteInterface;

class UserCampRouter
    extends DependencyLocator
    implements 	RouteInterface
    ,			UserRepositoryProvider
    ,			CampRepositoryProvider
{

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

        if ($user == null) {
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

        $user = $this->ecampCore_UserRepo()->findOneBy(
            array('username' => $userName));

        if ($user != null) {
            $length += strlen($userName);

            $camp = $this->ecampCore_CampRepo()->findOneBy(
                array('name' => $campName, 'owner' => $user));

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
        $user = $this->ecampCore_UserRepo()->find($userId);

        $campId = $params['camp'];
        $camp = ($campId != null) ? $this->ecampCore_CampRepo()->find($campId) : null;

        $path = "/" . $user->getUsername();

        if ($camp) {
            $path .= "/" . $camp->getName();
        }

        return $path;
    }

    public function getAssembledParams()
    {
        return array();
    }
}
