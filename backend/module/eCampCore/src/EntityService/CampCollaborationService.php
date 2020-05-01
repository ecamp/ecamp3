<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\CampCollaboration;
use eCamp\Core\Entity\User;
use eCamp\Core\Hydrator\CampCollaborationHydrator;
use eCamp\Lib\Service\ServiceUtils;
use Zend\Authentication\AuthenticationService;
use ZF\ApiProblem\ApiProblem;

class CampCollaborationService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            CampCollaboration::class,
            CampCollaborationHydrator::class,
            $authenticationService
        );
    }

    /**
     * @param mixed $data
     * @param mixed $persist
     *
     * @throws ORMException
     * @throws \Exception
     *
     * @return ApiProblem|CampCollaboration
     */
    public  function create($data, bool $persist = true) {
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
        $campCollaboration = parent::create($data, $persist);
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

    public function patch($id, $data) {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = parent::patch($id, $data);

        if ($campCollaboration->isEstablished()) {
            $campCollaboration = $this->updateCollaboration($campCollaboration, $data);
        } elseif ($campCollaboration->isInvitation()) {
            $campCollaboration = $this->updateInvitation($campCollaboration, $data);
        } elseif ($campCollaboration->isRequest()) {
            $campCollaboration = $this->updateRequest($campCollaboration, $data);
        }

        return $campCollaboration;
    }

    /**
     * @param mixed $id
     * @param mixed $data
     *
     * @throws \Exception
     *
     * @return ApiProblem|CampCollaboration
     */
    public function update($id, $data) {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = parent::update($id, $data);

        if ($campCollaboration->isEstablished()) {
            $campCollaboration = $this->updateCollaboration($campCollaboration, $data);
        } elseif ($campCollaboration->isInvitation()) {
            $campCollaboration = $this->updateInvitation($campCollaboration, $data);
        } elseif ($campCollaboration->isRequest()) {
            $campCollaboration = $this->updateRequest($campCollaboration, $data);
        }

        return $campCollaboration;
    }

    public function delete($id) {
        return parent::delete($id);
    }

    protected function fetchAllQueryBuilder($params = []) {
        $q = parent::fetchAllQueryBuilder($params);
        $q->andWhere($this->createFilter($q, Camp::class, 'row', 'camp'));

        if (isset($params['camp_id'])) {
            $q->andWhere('row.camp = :campId');
            $q->setParameter('campId', $params['camp_id']);
        }

        if (isset($params['user_id'])) {
            $q->andWhere('row.user = :userId');
            $q->setParameter('userId', $params['user_id']);
        }

        return $q;
    }

    /**
     * @param $data
     *
     * @throws \Exception
     *
     * @return CampCollaboration
     */
    private function updateCollaboration(CampCollaboration $campCollaboration, $data) {
        // TODO: ACL-Check can update Collaboration

        if (isset($data->role)) {
            $campCollaboration->setRole($data->role);
        }

        if (isset($data->status) && CampCollaboration::STATUS_UNRELATED == $data->status) {
            $this->delete($campCollaboration->getId());
            $campCollaboration = null;
        }

        return $campCollaboration;
    }

    /**
     * @param $data
     *
     * @throws \Exception
     *
     * @return CampCollaboration
     */
    private function updateInvitation(CampCollaboration $campCollaboration, $data) {
        $authUser = $this->getAuthUser();

        // TODO: ACL-Check can update Invitation

        if ($authUser === $campCollaboration->getUser()) {
            if (isset($data->status)) {
                if (CampCollaboration::STATUS_UNRELATED == $data->status) {
                    $this->delete($campCollaboration->getId());
                    $campCollaboration = null;
                }
                if (CampCollaboration::STATUS_ESTABLISHED == $data->status) {
                    $campCollaboration->setStatus(CampCollaboration::STATUS_ESTABLISHED);
                }
            }
        } else {
            if (isset($data->role)) {
                $campCollaboration->setRole($data->role);
            }
            if (isset($data->status)) {
                if (CampCollaboration::STATUS_UNRELATED == $data->status) {
                    $this->delete($campCollaboration->getId());
                    $campCollaboration = null;
                }
            }
        }

        return $campCollaboration;
    }

    /**
     * @param $data
     *
     * @throws ORMException
     * @throws \Exception
     *
     * @return CampCollaboration
     */
    private function updateRequest(CampCollaboration $campCollaboration, $data) {
        $authUser = $this->getAuthUser();

        // TODO: ACL-Check can update Request

        if ($authUser === $campCollaboration->getUser()) {
            if (isset($data->role)) {
                $campCollaboration->setRole($data->role);
            }
            if (isset($data->status)) {
                if (CampCollaboration::STATUS_UNRELATED == $data->status) {
                    $this->delete($campCollaboration->getId());
                    $campCollaboration = null;
                }
            }
        } else {
            if (isset($data->status)) {
                if (CampCollaboration::STATUS_UNRELATED == $data->status) {
                    $this->delete($campCollaboration->getId());
                    $campCollaboration = null;
                }
                if (CampCollaboration::STATUS_ESTABLISHED == $data->status) {
                    if (isset($data->role)) {
                        $campCollaboration->setRole($data->role);
                    }
                    $campCollaboration->setCollaborationAcceptedBy($authUser->getUsername());
                    $campCollaboration->setStatus(CampCollaboration::STATUS_ESTABLISHED);
                }
            }
        }

        return $campCollaboration;
    }
}
