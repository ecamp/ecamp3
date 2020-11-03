<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\CampCollaboration;
use eCamp\Core\Entity\User;
use eCamp\Core\Hydrator\CampCollaborationHydrator;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Entity\BaseEntity;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\ApiTools\ApiProblem\ApiProblem;
use Laminas\Authentication\AuthenticationService;

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
     *
     * @throws ORMException
     * @throws \Exception
     *
     * @return ApiProblem|CampCollaboration
     */
    protected function createEntity($data) {
        $authUser = $this->getAuthUser();
        if (!isset($data->userId)) {
            $data->userId = $authUser->getId();
        }

        /** @var Camp $camp */
        $camp = $this->findRelatedEntity(Camp::class, $data, 'campId');

        /** @var User $user */
        $user = $this->findRelatedEntity(User::class, $data, 'userId');

        if (!isset($data->role)) {
            $data->role = CampCollaboration::ROLE_MEMBER;
        }

        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = parent::createEntity($data);
        $campCollaboration->setCamp($camp);
        $campCollaboration->setUser($user);
        $campCollaboration->setRole($data->role);

        $this->assertAllowed($campCollaboration, Acl::REST_PRIVILEGE_CREATE);

        if ($data->userId === $authUser->getId()) {
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
     * @param $data
     *
     * @throws ORMException
     *
     * @return CampCollaboration
     */
    protected function patchEntity(BaseEntity $entity, $data) {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = $entity;

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
     * @param mixed $data
     *
     * @throws ORMException
     *
     * @return CampCollaboration
     */
    protected function updateEntity(BaseEntity $entity, $data) {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = parent::updateEntity($entity, $data);

        if ($campCollaboration->isEstablished()) {
            $campCollaboration = $this->updateCollaboration($campCollaboration, $data);
        } elseif ($campCollaboration->isInvitation()) {
            $campCollaboration = $this->updateInvitation($campCollaboration, $data);
        } elseif ($campCollaboration->isRequest()) {
            $campCollaboration = $this->updateRequest($campCollaboration, $data);
        }

        return $campCollaboration;
    }

    protected function fetchAllQueryBuilder($params = []) {
        $q = parent::fetchAllQueryBuilder($params);
        $q->andWhere($this->createFilter($q, Camp::class, 'row', 'camp'));

        if (isset($params['campId'])) {
            $q->andWhere('row.camp = :campId');
            $q->setParameter('campId', $params['campId']);
        }

        if (isset($params['userId'])) {
            $q->andWhere('row.user = :userId');
            $q->setParameter('userId', $params['userId']);
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
