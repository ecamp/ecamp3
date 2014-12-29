<?php
namespace EcampApi\Resource\Camp;

use EcampApi\Resource\Collaboration\CollaborationDetailResource;
use EcampLib\Resource\BaseResourceListener;
use PhlyRestfully\ResourceEvent;
use Zend\EventManager\EventManagerInterface;

class CollaborationResourceListener extends BaseResourceListener
{

    /**
     * @return \EcampCore\Repository\CampRepository
     */
    protected function getCampRepository()
    {
        return $this->getService('EcampCore\Repository\Camp');
    }

    /**
     * @return \EcampCore\Repository\UserRepository
     */
    protected function getUserRepository()
    {
        return $this->getService('EcampCore\Repository\User');
    }

    /**
     * @return \EcampCore\Repository\CampCollaborationRepository
     */
    protected function getCollaborationRepository()
    {
        return $this->getService('EcampCore\Repository\CampCollaboration');
    }

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('fetch', array($this, 'onFetch'));
        $this->listeners[] = $events->attach('fetchAll', array($this, 'onFetchAll'));

    }

    public function onFetch(ResourceEvent $e)
    {
        $camp = $this->getCamp($e);
        $user = $this->getUser($e);

        $campCollaboration =
            $this->getCollaborationRepository()->findByCampAndUser($camp, $user);

        $collaborationResource =
            new CollaborationDetailResource($user->getId(), $campCollaboration, $camp, $user);

        $identifiedUser = $this->getIdentifiedUser();
        if ($identifiedUser != null) {
            $collaborationResource->setVisitor($identifiedUser);
        }

        return $collaborationResource;
    }

    public function onFetchAll(ResourceEvent $e)
    {
        $params = $e->getQueryParams()->toArray();
        $params['user'] = $this->getUser($e);
        $params['camp'] = $this->getCamp($e);

        return $this->getCollaborationRepository()->getCollection($params);
    }

    /**
     * @param  ResourceEvent          $e
     * @return \EcampCore\Entity\Camp
     */
    protected function getCamp(ResourceEvent $e)
    {
        $campId = $e->getRouteParam('camp', $e->getQueryParam('camp'));
        $camp = ($campId != null) ? $this->getCampRepository()->find($campId) : null;

        return $camp;
    }

    /**
     * @param  ResourceEvent          $e
     * @return \EcampCore\Entity\User
     */
    protected function getUser(ResourceEvent $e)
    {
        $userId = $e->getRouteParam('collaborator', $e->getQueryParam('collaborator'));
        $user = ($userId != null) ? $this->getUserRepository()->find($userId) : null;

        return $user;
    }

}
