<?php
namespace EcampApi\Resource\Search;

use EcampLib\Resource\BaseResourceListener;
use PhlyRestfully\ResourceEvent;
use Zend\EventManager\EventManagerInterface;

class UserResourceListener extends BaseResourceListener
{
    /**
     * @return \EcampCore\Repository\UserRepository
     */
    protected function getUserRepository(){
        return $this->getService('EcampCore\Repository\User');
    }

    /**
     * @return \EcampCore\Repository\GroupRepository
     */
    protected function getGroupRepository(){
        return $this->getService('EcampCore\Repository\Group');
    }

    /**
     * @return \EcampCore\Repository\CampRepository
     */
    protected function getCampRepository(){
        return $this->getService('EcampCore\Repository\Camp');
    }


    protected $collectionRenderer;

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach('fetchAll', array($this, 'onFetchAll'));

        $this->collectionRenderer = new UserCollectionRenderingListener();
        $events->attachAggregate($this->collectionRenderer);
    }

    public function onFetchAll(ResourceEvent $e)
    {
        $showMembershipOfGroup = $e->getQueryParam('showMembershipOfGroup');
        $showCollaborationOfCamp = $e->getQueryParam('showCollaborationOfCamp');

        if ($showMembershipOfGroup != null) {
            $this->collectionRenderer->group = $this->getGroupRepository()->find($showMembershipOfGroup);
        }
        if ($showCollaborationOfCamp != null) {
            $this->collectionRenderer->camp = $this->getCampRepository()->find($showCollaborationOfCamp);
        }

        $search = $e->getQueryParam('search');
        $search = trim($search);

        if (strstr($search, ' ') || strlen($search) >= 3) {
            return $this->getUserRepository()->getSearchResult($search);
        } else {
            return array();
        }

    }
}
