<?php
namespace EcampApi\Resource\User;

use EcampCore\Resource\BaseResourceListener;
use PhlyRestfully\Exception\DomainException;
use PhlyRestfully\ResourceEvent;
use Zend\EventManager\EventManagerInterface;

class UserResourceListener extends BaseResourceListener
{
    /**
     * @return \EcampCore\Repository\UserRepository
     */
    protected function getUserRepository()
    {
        return $this->getService('EcampCore\Repository\User');
    }

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('fetch', array($this, 'onFetch'));
        $this->listeners[] = $events->attach('fetchAll', array($this, 'onFetchAll'));
        $this->listeners[] = $events->attach('update', array($this, 'onUpdate'));

    }

    public function onFetch(ResourceEvent $e)
    {
        $id = $e->getParam('id');
        $user = $this->getUserRepository()->find($id);

        if (!$user) {
            throw new DomainException('User not found', 404);
        }

        return new UserDetailResource($user);
    }

    public function onFetchAll(ResourceEvent $e)
    {
        return $this->getUserRepository()->getCollection($e->getQueryParams()->toArray());
    }

    public function onUpdate(ResourceEvent $e)
    {
        $id = $e->getParam('id');
        $data = $e->getParam('data');
        $dataArray = (array) $data;

        $user = $this->getUserService()->Update($id, $dataArray);

        return new UserDetailResource($user);
    }

}
