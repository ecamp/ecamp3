<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\Expr;
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
        $this->assertAuthenticated();

        $authUser = $this->getAuthUser();
        if (!isset($data->userId)) {
            $data->userId = null;
        }
        if (null == $data->userId && !$data->inviteEmail) {
            $data->userId = $authUser->getId();
        }

        /** @var Camp $camp */
        $camp = $this->findRelatedEntity(Camp::class, $data, 'campId');

        /** @var User $user */
        $user = null != $data->userId ? $this->findRelatedEntity(User::class, $data, 'userId') : null;

        $q = $this->fetchAllQueryBuilder();
        $expr = new Expr();
        $q->andWhere($expr->andX(
            $q->expr()->eq('row.camp', ':campId'),
            $expr->orX(
                $expr->eq('row.user', ':userId'),
                $expr->eq('row.inviteEmail', ':inviteEmail')
            )
        ));
        $q->setParameter('campId', $camp->getId());
        $q->setParameter('userId', null != $user ? $user->getId() : null);
        $q->setParameter('inviteEmail', $data->inviteEmail);
        $result = $q->getQuery()->getResult();

        if (count($result) > 0) {
            throw new \Error("Cannot create CampCollaboration with same inviteEmail {$data->inviteEmail} or userId {$data->userId}, already exists");
        }
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = parent::createEntity($data);
        $camp->addCampCollaboration($campCollaboration);
        if (null != $user) {
            $user->addCampCollaboration($campCollaboration);
        }

        if (!isset($data->role)) {
            $data->role = CampCollaboration::ROLE_MEMBER;
        }
        $campCollaboration->setRole($data->role);

        $this->assertAllowed($campCollaboration, Acl::REST_PRIVILEGE_CREATE);

        if ($data->userId === $authUser->getId()) {
            if ($data->userId === $camp->getCreator()->getId()) {
                // Create CampCollaboration for Creator
                $campCollaboration->setStatus(CampCollaboration::STATUS_ESTABLISHED);
                $campCollaboration->setCollaborationAcceptedBy($authUser->getUsername());
            } else {
                // Create CampCollaboration for AuthUser
                $campCollaboration->setStatus(CampCollaboration::STATUS_REQUESTED);
            }
        } else {
            // Create CampCollaboration for other User
            $campCollaboration->setStatus(CampCollaboration::STATUS_INVITED);
            $campCollaboration->setCollaborationAcceptedBy($authUser->getUsername());
            $campCollaboration->setInviteEmail($data->inviteEmail);
        }

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

    /**
     * @throws ORMException
     *
     * @return bool
     */
    protected function deleteEntity(BaseEntity $entity) {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = $entity;
        $data = (object) ['status' => CampCollaboration::STATUS_LEFT];

        if ($campCollaboration->isEstablished()) {
            $campCollaboration = $this->updateCollaboration($campCollaboration, $data);
        } elseif ($campCollaboration->isInvitation()) {
            $campCollaboration = $this->updateInvitation($campCollaboration, $data);
        } elseif ($campCollaboration->isRequest()) {
            $campCollaboration = $this->updateRequest($campCollaboration, $data);
        }
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

        if (isset($data->status)) {
            if (CampCollaboration::STATUS_LEFT == $data->status) {
                $campCollaboration->setStatus(CampCollaboration::STATUS_LEFT);
            }
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
                switch ($data->status) {
                    case CampCollaboration::STATUS_LEFT:
                    case CampCollaboration::STATUS_UNRELATED:
                        $campCollaboration->setStatus(CampCollaboration::STATUS_LEFT);

                    break;
                    case CampCollaboration::STATUS_ESTABLISHED:
                        $campCollaboration->setStatus(CampCollaboration::STATUS_ESTABLISHED);

                    break;
                }
            }
        } else {
            if (isset($data->role)) {
                $campCollaboration->setRole($data->role);
            }
            if (isset($data->status)) {
                switch ($data->status) {
                    case CampCollaboration::STATUS_LEFT:
                    case CampCollaboration::STATUS_UNRELATED:
                        $campCollaboration->setStatus(CampCollaboration::STATUS_LEFT);

                    break;
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
                switch ($data->status) {
                    case CampCollaboration::STATUS_LEFT:
                    case CampCollaboration::STATUS_UNRELATED:
                        $campCollaboration->setStatus(CampCollaboration::STATUS_LEFT);

                    break;
                }
            }
        } else {
            if (isset($data->status)) {
                switch ($data->status) {
                    case CampCollaboration::STATUS_LEFT:
                    case CampCollaboration::STATUS_UNRELATED:
                        $campCollaboration->setStatus(CampCollaboration::STATUS_LEFT);

                    break;
                    case CampCollaboration::STATUS_ESTABLISHED:
                        if (isset($data->role)) {
                            $campCollaboration->setRole($data->role);
                        }
                        $campCollaboration->setCollaborationAcceptedBy($authUser->getUsername());
                        $campCollaboration->setStatus(CampCollaboration::STATUS_ESTABLISHED);

                    break;
                }
            }
        }

        return $campCollaboration;
    }
}
