<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\CampCollaboration;
use eCamp\Core\Entity\User;
use eCamp\Core\Hydrator\CampCollaborationHydrator;
use eCamp\Core\Service\SendmailService;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Entity\BaseEntity;
use eCamp\Lib\Service\EntityValidationException;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

class CampCollaborationService extends AbstractEntityService {
    private MaterialListService $materialListService;
    private SendmailService $sendmailService;

    public function __construct(
        ServiceUtils $serviceUtils,
        AuthenticationService $authenticationService,
        MaterialListService $materialListService,
        SendmailService $sendmailService
    ) {
        parent::__construct(
            $serviceUtils,
            CampCollaboration::class,
            CampCollaborationHydrator::class,
            $authenticationService
        );

        $this->materialListService = $materialListService;
        $this->sendmailService = $sendmailService;
    }

    /**
     * @param mixed $data
     *
     * @throws ORMException
     * @throws \Exception
     */
    protected function createEntity($data): CampCollaboration {
        $this->assertAuthenticated();

        $authUser = $this->getAuthUser();
        if (!isset($data->userId)) {
            $data->userId = null;
        }
        $inviteEmail = isset($data->inviteEmail) ? $data->inviteEmail : null;
        if (null == $data->userId && !$inviteEmail) {
            $data->userId = $authUser->getId();
        }

        /** @var Camp $camp */
        $camp = $this->findRelatedEntity(Camp::class, $data, 'campId');

        $user = null;
        if (null != $data->userId) {
            /** @var User $user */
            $user = $this->findRelatedEntity(User::class, $data, 'userId');
        }

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
        $userForCampCollaborationAlreadyKnown = null != $user;
        $q->setParameter('userId', $userForCampCollaborationAlreadyKnown ? $user->getId() : null);
        $q->setParameter('inviteEmail', $inviteEmail);
        $result = $q->getQuery()->getResult();

        if (count($result) > 0) {
            $messages = [];
            $userId = isset($data->userId) ? $data->userId : null;
            if ($userId) {
                $messages['userId'] = ['duplicateCampCollaboration' => "CampCollaboration for the camp {$data->campId} and user {$userId} already exists."];
            }
            if ($inviteEmail) {
                $messages['inviteEmail'] = ['duplicateCampCollaboration' => "CampCollaboration for the camp {$data->campId} and inviteEmail {$data->inviteEmail} already exists."];
            }

            throw (new EntityValidationException())->setMessages($messages);
        }
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = parent::createEntity($data);
        $camp->addCampCollaboration($campCollaboration);
        if ($userForCampCollaborationAlreadyKnown) {
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
            $campCollaboration->setInviteEmail($inviteEmail);
        }

        if (CampCollaboration::STATUS_INVITED == $campCollaboration->getStatus() && $campCollaboration->getInviteEmail()) {
            $uniqid = uniqid('', true);
            $campCollaboration->setInviteKey($uniqid);
            $this->sendmailService->sendInviteToCampMail(
                $authUser,
                $camp,
                $uniqid,
                $campCollaboration->getInviteEmail()
            );
        }

        return $campCollaboration;
    }

    protected function createEntityPost(BaseEntity $entity, $data): CampCollaboration {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = $entity;

        if (CampCollaboration::STATUS_ESTABLISHED === $campCollaboration->getStatus()) {
            $this->createMaterialList($campCollaboration);
        }

        return $campCollaboration;
    }

    /**
     * @param $data
     *
     * @throws ORMException
     */
    protected function patchEntity(BaseEntity $entity, $data): CampCollaboration {
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
     */
    protected function updateEntity(BaseEntity $entity, $data): CampCollaboration {
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
     */
    protected function deleteEntity(BaseEntity $entity) {
        /** @var CampCollaboration $campCollaboration */
        $campCollaboration = $entity;
        if (in_array(
            $campCollaboration->getStatus(),
            [CampCollaboration::STATUS_INVITED, CampCollaboration::STATUS_REQUESTED],
            true
        )) {
            parent::deleteEntity($entity);
        } else {
            $this->updateEntity($campCollaboration, (object) ['status' => CampCollaboration::STATUS_LEFT]);
        }
    }

    protected function fetchAllQueryBuilder($params = []): QueryBuilder {
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
                        $this->createMaterialList($campCollaboration);

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
                        $this->createMaterialList($campCollaboration);

                    break;
                }
            }
        }

        return $campCollaboration;
    }

    private function createMaterialList(CampCollaboration $campCollaboration) {
        $this->materialListService->create((object) [
            'campId' => $campCollaboration->getCamp()->getId(),
            'name' => $campCollaboration->getUser()->getDisplayName(),
        ]);
    }
}
