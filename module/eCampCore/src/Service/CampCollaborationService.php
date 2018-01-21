<?php

namespace eCamp\Core\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use eCamp\Core\Hydrator\CampCollaborationHydrator;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\CampCollaboration;
use eCamp\Core\Entity\User;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Service\BaseService;
use ZF\ApiProblem\ApiProblem;

class CampCollaborationService extends BaseService
{
    public function __construct
    ( Acl $acl
    , EntityManager $entityManager
    , CampCollaborationHydrator $campCollaborationHydrator
    ) {
        parent::__construct
        ( $acl
        , $entityManager
        , $campCollaborationHydrator
        , CampCollaboration::class
        );
    }

    /**
     * @param mixed $data
     * @return CampCollaboration|ApiProblem
     * @throws ORMException
     * @throws \Exception
     */
    public function create($data) {
        $authUser = $this->getAuthUser();
        if (!isset($data->user_id)) {
            $data->user_id = $authUser->getId();
        }

        /** @var Camp $camp */
        $camp = $this->findEntity(Camp::class, $data->camp_id);
        /** @var User $user */
        $user = $this->findEntity(User::class, $data->user_id);

        if (!isset($data->role)) {
            $data->role = CampCollaboration::ROLE_MEMBER;
        }

        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = parent::create($data);
        $campCollaboration->setCamp($camp);
        $campCollaboration->setUser($user);
        $campCollaboration->setRole($data->role);

        if ($data->user_id === $authUser->getId()) {
            // Create CampCollaboration for AuthUser
            $campCollaboration->setStatus(CampCollaboration::STATUS_REQUESTED);

        } else {
            // Create CampCollaboration for other User
            $campCollaboration->setStatus(CampCollaboration::STATUS_INVITED);
            $campCollaboration->setCollaborationAcceptedBy($authUser->getUsername());
        }

        $camp->addCampCollaboration($campCollaboration);
        $user->addCampCollaboration($campCollaboration);

        return $campCollaboration;
    }

    /**
     * @param mixed $id
     * @param mixed $data
     * @return CampCollaboration|ApiProblem
     * @throws \Exception
     */
    public function update($id, $data) {

        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = parent::update($id, $data);

        if ($campCollaboration->isEstablished()) {
            $this->updateCollaboration($campCollaboration, $data);
        } elseif ($campCollaboration->isInvitation()) {
            $this->updateInvitation($campCollaboration, $data);
        } elseif ($campCollaboration->isRequest()) {
            $this->updateRequest($campCollaboration, $data);
        }

        return $campCollaboration;
    }


    /**
     * @param CampCollaboration $campCollaboration
     * @param $data
     * @throws \Exception
     */
    private function updateCollaboration(CampCollaboration $campCollaboration, $data) {
        // TODO: ACL-Check can update Collaboration

        if (isset($data->role)) { $campCollaboration->setRole($data->role); }

        if (isset($data->status) && $data->status == CampCollaboration::STATUS_UNRELATED) {
            $this->delete($campCollaboration->getId());
        }
    }


    /**
     * @param CampCollaboration $campCollaboration
     * @param $data
     * @throws \Exception
     */
    private function updateInvitation(CampCollaboration $campCollaboration, $data) {
        $authUser = $this->getAuthUser();

        // TODO: ACL-Check can update Invitation

        if ($authUser === $campCollaboration->getUser()) {
            if ($data->status == CampCollaboration::STATUS_UNRELATED) {
                $this->delete($campCollaboration->getId());
            }
            if ($data->status == CampCollaboration::STATUS_ESTABLISHED) {
                $campCollaboration->setStatus(CampCollaboration::STATUS_ESTABLISHED);
            }
        } else {
            if ($data->status == CampCollaboration::STATUS_UNRELATED) {
                $this->delete($campCollaboration->getId());
            }
            if(isset($data->role)) { $campCollaboration->setRole($data->role); }
        }
    }

    /**
     * @param CampCollaboration $campCollaboration
     * @param $data
     * @throws ORMException
     * @throws \Exception
     */
    private function updateRequest(CampCollaboration $campCollaboration, $data) {
        $authUser = $this->getAuthUser();

        // TODO: ACL-Check can update Request

        if ($authUser === $campCollaboration->getUser()) {
            if(isset($data->role)) { $campCollaboration->setRole($data->role); }
            if ($data->status == CampCollaboration::STATUS_UNRELATED) {
                $this->delete($campCollaboration->getId());
            }
        } else {
            if ($data->status == CampCollaboration::STATUS_UNRELATED) {
                $this->delete($campCollaboration->getId());
            }
            if ($data->status == CampCollaboration::STATUS_ESTABLISHED) {
                if(isset($data->role)) { $campCollaboration->setRole($data->role); }
                $campCollaboration->setCollaborationAcceptedBy($authUser->getUsername());
                $campCollaboration->setStatus(CampCollaboration::STATUS_ESTABLISHED);
            }
        }
    }


}
