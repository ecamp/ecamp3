<?php

namespace EcampCore\Router;

use EcampCore\Repository\Provider\GroupRepositoryProvider;

use Zend\Mvc\Router\Http\RouteMatch;

use EcampCore\DI\DependencyLocator;
use EcampCore\Repository\Provider\CampRepositoryProvider;

use Zend\Mvc\Router\Exception;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Mvc\Router\Http\RouteInterface;

class GroupCampRouter
    extends DependencyLocator
    implements 	RouteInterface
    ,			GroupRepositoryProvider
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

        $group  = $traceInfo['group'];
        $camp   = $traceInfo['camp'];
        $length = $traceInfo['length'];

        if ($group == null) {
            return null;
        } else {
            $groupId = $group->getId();
            $campId  = ($camp != null) ? $camp->getId() : null;

            $params = array_merge(array('group' => $groupId, 'camp' => $campId), $this->defaults);

            return new RouteMatch($params, $length);
        }
    }

    private function traceCamp($path)
    {
        $trim_path = ltrim($path, '/');
        $length = strlen($path) - strlen($trim_path);

        $names = explode("/", $trim_path);
        $name = array_shift($names);

        $group = $this->ecampCore_GroupRepo()->findOneBy(
            array('name' => $name, 'parent' => null));

        if ($group != null) {
            $length += strlen($name);

            while (count($names)) {
                $name = array_shift($names);

                $childGroup = $this->ecampCore_GroupRepo()->findOneBy(
                    array('name' => $name, 'parent' => $group));

                if ($childGroup != null) {
                    $group = $childGroup;
                    $length += (strlen($group->getName()) + 1);
                } else {
                    break;
                }
            }

            $camp = $this->ecampCore_CampRepo()->findOneBy(
                array('name' => $name, 'group' => $group));

            if ($camp != null) {
                $length += (strlen($camp->getName()) + 1);
            }
        }

        return array('group' => $group, 'camp' => $camp, 'length' => $length);
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
        $campId  = $params['camp'];

        $group = $this->ecampCore_GroupRepo()->find($groupId);
        $groups = array();

        $camp = ($campId != null) ? $this->ecampCore_CampRepo()->find($campId) : null;

        while ($group != null) {
            array_unshift($groups, $group->getName());
            $group = $group->getParent();
        }

        $path = "/" . implode("/", $groups);

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
