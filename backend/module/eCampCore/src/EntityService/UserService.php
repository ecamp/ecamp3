<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\Expr;
use eCamp\Core\Entity\User;
use eCamp\Core\Hydrator\UserHydrator;
use eCamp\Core\Repository\UserRepository;
use eCamp\Core\Service\SendmailService;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\ServiceUtils;
use Hybridauth\User\Profile;
use Zend\Authentication\AuthenticationService;
use ZF\ApiProblem\ApiProblem;

/**
 * Class UserService
 */
class UserService extends AbstractEntityService {
    /**
     * @var SendmailService
     */
    private $sendmailService;

    public function __construct(
        ServiceUtils $serviceUtils,
        AuthenticationService $authenticationService,
        SendmailService $sendmailService
    ) {
        parent::__construct(
            $serviceUtils,
            User::class,
            UserHydrator::class,
            $authenticationService
        );

        $this->sendmailService = $sendmailService;
    }

    public function findCollectionQueryBuilder($className, $alias, $params) {
        $q = parent::findCollectionQueryBuilder($className, $alias, $params);
        if (isset($params['search'])) {
            $expr = new Expr();
            $q->andWhere($expr->orX(
                $expr->like($expr->lower($alias . '.username'), ':search')
            ));
            $q->setParameter('search', '%' . strtolower($params['search']) . '%');
        }

        return $q;
    }

    public function findByMail($email) {
        /** @var UserRepository $repository */
        $repository = $this->getRepository();
        $repository->findByMail($email);
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
            // Send Activtion Mail:
            $this->sendmailService->sendRegisterMail($user, $key);


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

    public function findByUsername($username) {
        /** @var UserRepository $userRepository */
        $userRepository = $this->getRepository();
        return $userRepository->findByUsername($username);
    }
}
