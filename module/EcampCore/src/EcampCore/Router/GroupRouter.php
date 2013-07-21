<?php

namespace EcampCore\Router;

use Zend\Mvc\Router\Http\RouteMatch;

use Zend\Mvc\Router\Exception;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Mvc\Router\Http\RouteInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class GroupRouter
    implements 	RouteInterface
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

    public function getGroupRepository()
    {
        return $this->getServiceLocator()->getServiceLocator()->get('EcampCore\Repository\Group');
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
                $traceInfo = $this->traceGroup(substr($path, $pathOffset));
            } else {
                return null;
            }
        } else {
            $traceInfo = $this->traceGroup($path);
        }

        $group  = $traceInfo['group'];
        $length = $traceInfo['length'];

        if ($group == null) {
            return null;
        } else {
            $groupId = $group->getId();

            $params = array_merge(array('group' => $groupId), $this->defaults);

            return new RouteMatch($params, $length);
        }
    }

    private function traceGroup($path)
    {
        $trim_path = ltrim($path, '/');
        $length = strlen($path) - strlen($trim_path);

        $names = explode("/", $trim_path);
        $name = array_shift($names);

        $group = $this->getGroupRepository()->findOneBy(
            array('name' => $name, 'parent' => null));

        if ($group != null) {
            $length += strlen($name);

            while (count($names)) {
                $name = array_shift($names);

                $childGroup = $this->getGroupRepository()->findOneBy(
                    array('name' => $name, 'parent' => $group));

                if ($childGroup != null) {
                    $group = $childGroup;
                    $length += (strlen($group->getName()) + 1);
                } else {
                    break;
                }
            }
        }

        return array('group' => $group, 'length' => $length);
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
        $groupId = $params['group'];

        $group = $this->getGroupRepository()->find($groupId);
        $groups = array();

        while ($group != null) {
            array_unshift($groups, $group->getName());
            $group = $group->getParent();
        }

        $path = "/" . implode("/", $groups);

        return $path;
    }

    public function getAssembledParams()
    {
        return array();
    }
}
