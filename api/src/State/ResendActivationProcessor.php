<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\UserActivation;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\ReCaptcha\ReCaptchaWrapper;
use App\Service\MailService;
use App\Util\IdGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @implements ProcessorInterface<UserActivation,null>
 */
readonly class ResendActivationProcessor implements ProcessorInterface {
    public function __construct(
        private ReCaptchaWrapper $reCaptcha,
        private EntityManagerInterface $em,
        private UserRepository $userRepository,
        private MailService $mailService,
    ) {}

    /**
     * @param UserActivation $data
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): null {
        $resp = $this->reCaptcha->verify($data->recaptchaToken);
        if (!$resp->isSuccess()) {
            throw new HttpException(422, 'ReCaptcha failed');
        }

        $user = $this->userRepository->loadUserByIdentifier($data->email);

        if (null == $user) {
            return null;
        }

        if (User::STATE_REGISTERED !== $user->state) {
            return null;
        }

        $user->activationKey = IdGenerator::generateRandomHexString(64);
        $user->activationKeyHash = md5($user->activationKey);
        $this->em->flush();

        $this->mailService->sendUserActivationMail($user, $user->activationKey);

        return null;
    }
}
