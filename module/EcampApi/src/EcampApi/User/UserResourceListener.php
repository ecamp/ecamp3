<?php
namespace EcampApi\User;

use PhlyRestfully\Exception\CreationException;
use PhlyRestfully\Exception\DomainException;
use PhlyRestfully\ResourceEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;

class UserResourceListener extends AbstractListenerAggregate
{
    protected $repo;

    public function __construct(\EcampCore\Repository\UserRepository $repo)
    {
        $this->repo = $repo;
    }

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('fetch', array($this, 'onFetch'));
        $this->listeners[] = $events->attach('fetchAll', array($this, 'onFetchAll'));
    }

    public function onFetch(ResourceEvent $e)
    {
        $id = $e->getParam('id');
        $user = $this->repo->find($id);
        if (!$user) {
            throw new DomainException('User not found', 404);
        }
        return new UserDetailResource($user);
    }

    public function onFetchAll(ResourceEvent $e)
    {
    	return $this->repo->getApiCollection($e->getQueryParams()->toArray());
    }
}