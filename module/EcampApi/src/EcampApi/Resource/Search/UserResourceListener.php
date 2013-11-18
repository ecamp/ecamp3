<?php
namespace EcampApi\Resource\Search;

use PhlyRestfully\ResourceEvent;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;

class UserResourceListener extends AbstractListenerAggregate
{
    protected $userRepo;
    protected $groupRepo;
    protected $campRepo;
    protected $collectionRenderer;

    public function __construct(
        \EcampCore\Repository\UserRepository $userRepo,
        \EcampCore\Repository\GroupRepository $groupRepo,
        \EcampCore\Repository\CampRepository $campRepo
    ) {
        $this->userRepo = $userRepo;
        $this->groupRepo = $groupRepo;
        $this->campRepo = $campRepo;
    }

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
            $this->collectionRenderer->group = $this->groupRepo->find($showMembershipOfGroup);
        }
        if ($showCollaborationOfCamp != null) {
            $this->collectionRenderer->camp = $this->campRepo->find($showCollaborationOfCamp);
        }

        $search = $e->getQueryParam('search');
        $search = trim($search);

        if (strstr($search, ' ') || strlen($search) >= 3) {
            return $this->userRepo->getSearchResult($search);
        } else {
            return array();
        }

    }
}
