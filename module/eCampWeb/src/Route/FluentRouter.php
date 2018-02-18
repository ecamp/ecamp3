<?php

namespace eCamp\Web\Route;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Group;
use eCamp\Core\Entity\User;
use eCamp\Core\Repository\CampRepository;
use eCamp\Core\Repository\GroupRepository;
use eCamp\Core\Repository\UserRepository;
use Zend\Router\Exception\RuntimeException;
use Zend\Router\Http\RouteInterface;
use Zend\Router\RouteMatch;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Uri\Uri;

abstract class FluentRouter implements RouteInterface
{
    /** @var UserRepository */
    private $userRepository;

    /** @var GroupRepository */
    private $groupRepository;

    /** @var CampRepository */
    private $campRepository;

    /** @var array */
    protected $options;

    /**
     * FluentRouter constructor.
     * @param $userRepository
     * @param $groupRepository
     * @param $campRepository
     * @param array $options
     */
    public function __construct
    ( $userRepository
    , $groupRepository
    , $campRepository
    , $options = []
    ) {
        $this->userRepository = $userRepository;
        $this->groupRepository = $groupRepository;
        $this->campRepository = $campRepository;

        $this->options = $options;
    }


    /**
     * @param  array|\Traversable $options
     * @return void
     */
    public static function factory($options = []) {
        throw new RuntimeException("not implemented");
    }


    /**
     * @param  Request $request
     * @param null $pathOffset
     * @param array $options
     * @return null|RouteMatch
     */
    public function match(Request $request, $pathOffset = null, $options = []) {
        if (!method_exists($request, 'getUri')) {
            return null;
        }

        /** @var Uri $uri */
        $uri  = $request->getUri();
        $path = $uri->getPath();

        if (empty($path)) { return null; }
        if ($pathOffset == null) { $pathOffset = 0; }
        if ($pathOffset < 0) { return null; }
        if ($pathOffset > strlen($path)) { return null; }
        $path = substr($path, $pathOffset);
        $length = 0;

        if (substr($path, 0, 5) == 'user/') {
            $path = substr($path, 4);
            $length += 4;
            return $this->matchUser($path, $length, []);
        }

        if (substr($path, 0, 6) == 'group/') {
            $path = substr($path, 5);
            $length += 5;
            return $this->matchGroup($path, $length, []);
        }

        return null;
    }

    /**
     * @param $path
     * @param $length
     * @param array $params
     * @return null|RouteMatch
     */
    protected function matchUser($path, $length, $params){
        if (empty($path)) { return null; }
        if (substr($path, 0, 1) !== '/') { return null; }

        $path = substr($path, 1);
        $length += 1;
        if (empty($path)) { return null; }

        $elements = explode('/', $path);
        $username = array_shift($elements);

        /** @var User $user */
        $user = $this->userRepository->findOneBy([
            'username' => urldecode($username)
        ]);
        if ($user == null) { return null; }

        $usernameLength = strlen($username);
        $path = substr($path, $usernameLength);
        $length += $usernameLength;


        if (substr($path, 0, 6) == '/camp/') {
            $path = substr($path, 5);
            $length += 5;

            $params = array_merge([
                'campOwner' => $user,
                'campOwnerId' => $user->getId()
            ], $params);

            return $this->matchCamp($path, $length, $params);
        }

        $defaults = isset($this->options['defaults']) ? $this->options['defaults'] : [];

        $params = array_merge([
            'user' => $user,
            'userId' => $user->getId(),
        ], $params, $defaults);

        return new UserRouteMatch($params, $length);
    }


    /**
     * @param $path
     * @param $length
     * @param $params
     * @return null|RouteMatch
     */
    protected function matchGroup($path, $length, $params) {
        if (empty($path)) { return null; }
        if (substr($path, 0, 1) !== '/') { return null; }

        $groupnames = substr($path, 1);
        $elements = explode('/', $groupnames);

        $group = null;
        while (count($elements) > 0) {
            $groupname = array_shift($elements);

            $childGroup = $this->groupRepository->findOneBy([
                'parent' => $group,
                'name' => urldecode($groupname)
            ]);

            if ($childGroup == null) { break; }
            $groupnameLength = 1 + strlen($groupname);
            $path = substr($path, $groupnameLength);
            $length += $groupnameLength;
            $group = $childGroup;
        }
        if ($group == null) { return null; }

        if (substr($path, 0, 6) == '/camp/') {
            $path = substr($path, 5);
            $length += 5;

            $params = array_merge([
                'campOwner' => $group,
                'campOwnerId' => $group->getId()
            ], $params);

            return $this->matchCamp($path, $length, $params);
        }

        $defaults = isset($this->options['defaults']) ? $this->options['defaults'] : [];

        $params = array_merge([
            'group' => $group,
            'groupId' => $group->getId(),
        ], $params, $defaults);

        return new GroupRouteMatch($params, $length);
    }

    /**
     * @param $path
     * @param $length
     * @param $params
     * @return null|RouteMatch
     */
    protected function matchCamp($path, $length, $params) {
        if (empty($path)) { return null; }
        if (substr($path, 0, 1) !== '/') { return null; }

        $path = substr($path, 1);
        $length += 1;
        if (empty($path)) { return null; }

        $elements = explode('/', $path);
        $campname = array_shift($elements);

        /** @var Camp $camp */
        $camp = $this->campRepository->findOneBy([
            'owner' => $params['campOwner'],
            'name' => $campname
        ]);
        if ($camp == null) { return null; }

        $campnameLength = strlen($campname);
        $length += $campnameLength;

        $defaults = isset($this->options['defaults']) ? $this->options['defaults'] : [];

        $params = array_merge([
            'camp' => $camp,
            'campId' => $camp->getId(),
        ], $params, $defaults);

        return new CampRouteMatch($params, $length);
    }


    /**
     * @param  array $params
     * @param  array $options
     * @return mixed
     */
    public function assemble(array $params = [], array $options = []) {
        $path = '';
        $target = $this->getTarget($params);

        switch ($target) {
            case 'user':
                $path .= $this->assembleUser($params);
                break;
            case 'group':
                $path .= $this->assembleGroup($params);
                break;
            case 'camp':
                $path .= $this->assembleCamp($params);
                break;
        }

        return $path;
    }

    private function getTarget(array $params) {
        if (isset($params['target'])) { return $params['target']; }

        $target = null;
        if (isset($params['user'])) { $target = 'user'; }
        if (isset($params['userId'])) { $target = 'user'; }
        if (isset($params['group'])) { $target = 'group'; }
        if (isset($params['groupId'])) { $target = 'group'; }
        if (isset($params['camp'])) { $target = 'camp'; }
        if (isset($params['campId'])) { $target = 'camp'; }

        return $target;
    }

    function assembleUser(array $params = []) {
        /** @var User $user */
        $user = null;
        if ($user == null) {
            if (isset($params['user'])) {
                $user = $params['user'];
            }
        }
        if ($user == null) {
            if (isset($params['userId'])) {
                $userId = $params['userId'];
                $user = $this->userRepository->find($userId);
            }
        }

        $path = 'user';
        if ($user !== null) {
            $path .= '/' . $user->getUsername();
        }

        return $path;
    }

    function assembleGroup(array $params = []) {
        /** @var Group $group */
        $group = null;
        if ($group == null) {
            if (isset($params['group'])) {
                $group = $params['group'];
            }
        }
        if ($group == null) {
            if (isset($params['groupId'])) {
                $groupId = $params['groupId'];
                $group = $this->groupRepository->find($groupId);
            }
        }

        $path = 'group';
        foreach ($group->pathAsArray() as $g) {
            /** @var Group $g */
            $path .= ('/' . $g->getName());
        }

        return $path;
    }

    function assembleCamp(array $params = []) {
        /** @var Camp $camp */
        $camp = null;
        if ($camp == null) {
            if (isset($params['camp'])) {
                $camp = $params['camp'];
            }
        }
        if ($camp == null) {
            if (isset($params['campId'])) {
                $campId = $params['campId'];
                $camp = $this->campRepository->find($campId);
            }
        }

        $path = '';
        if ($camp != null) {
            $owner = $camp->getOwner();
            if ($owner instanceof User) {
                $params = array_merge(['user' => $owner], $params);
                $path = $this->assembleUser($params);

            } elseif ($owner instanceof Group) {
                $params = array_merge(['group' => $owner], $params);
                $path = $this->assembleGroup($params);

            }

            $path .= '/camp';
            $path .= ('/' . $camp->getName());
        }

        return $path;
    }


    /**
     * @return array
     */
    public function getAssembledParams() {
        return [];
    }

}