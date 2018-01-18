<?php

namespace eCamp\Core\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use eCamp\Core\Hydrator\UserHydrator;
use eCamp\Core\Entity\User;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\BaseService;
use ZF\ApiProblem\ApiProblem;

class UserService extends BaseService
{
    public function __construct(Acl $acl, EntityManager $entityManager) {
        parent::__construct($acl, $entityManager, User::class, UserHydrator::class);
    }

    /**
     * @param mixed $data
     * @return User|mixed|ApiProblem
     * @throws ORMException
     * @throws NoAccessException
     */
    public function create($data) {
        $state = property_exists($data, 'state') ? $data->state : User::STATE_NONREGISTERED;

        if (!in_array($state, [User::STATE_NONREGISTERED, User::STATE_REGISTERED])) {
            return new ApiProblem(400, 'Invalid state: ' . $state);
        }

        /** @var User $user */
        $user = parent::create($data);
        $user->setRole(User::ROLE_USER);
        $user->setState($state);

        $key = $user->setMailAddress($data->mailAddress);
        // TODO: Send Registration-Mail with $key

        return $user;
    }

}
