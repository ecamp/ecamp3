<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\User;
use eCamp\Core\Hydrator\UserHydrator;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\ServiceUtils;
use Hybridauth\User\Profile;
use Zend\Authentication\AuthenticationService;
use ZF\ApiProblem\ApiProblem;

/**
 * Class UserService
 */
class UserService extends AbstractEntityService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            $authenticationService,
            User::class,
            UserHydrator::class
        );
    }

    /**
     * @param mixed $data
     * @return User|mixed|ApiProblem
     * @throws NoAccessException
     * @throws ORMException
     * @throws \Exception
     */
    public function create($data) {
        /** @var Profile $profile */
        $profile = $data;

        if ($profile instanceof Profile) {
            $data = (object)[
                'username' => $profile->displayName,
                'mailAddress' => $profile->email,
                'state' => User::STATE_REGISTERED,
            ];
        }

        $state = isset($data->state) ? $data->state : User::STATE_NONREGISTERED;
        if (!in_array($state, [User::STATE_NONREGISTERED, User::STATE_REGISTERED])) {
            return new ApiProblem(400, 'Invalid state: ' . $state);
        }

        /** @var User $user */
        $user = parent::create($data);
        $user->setState($state);
        $user->setRole(User::ROLE_USER);

        $key = $user->setMailAddress($data->mailAddress);

        if ($profile instanceof Profile) {
            $user->verifyMailAddress($key);
        } else {
            // TODO: Send Activtion Mail:
            //...

            // TODO: Remove Dev-Code
            // Dev: Registrierte Benutzer sofort freischalten
            //      Keine Aktivierung mit Mail notwendig
            if ($user->getState() == User::STATE_REGISTERED) {
                $user->verifyMailAddress($key);
            }
        }



        return $user;
    }

    /**
     * @param mixed $id
     * @param mixed $data
     * @return User|ApiProblem
     * @throws NoAccessException
     */
    public function update($id, $data) {
        if ($data instanceof Profile) {
            /** @var Profile $profile */
            $profile = $data;
            $data = (object)['username' => $profile->displayName];
        }
        return parent::update($id, $data);
    }
}
