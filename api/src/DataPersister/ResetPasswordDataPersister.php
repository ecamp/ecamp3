<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\DataPersister\Util\DataPersisterObservable;
use App\DTO\ResetPassword;
use App\Repository\UserRepository;
use App\Service\MailService;
use App\Util\IdGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class ResetPasswordDataPersister implements ContextAwareDataPersisterInterface {
    public function __construct(
        private DataPersisterObservable $dataPersisterObservable,
        private EntityManagerInterface $em,
        private UserRepository $userRepository,
        private PasswordHasherFactoryInterface $pwHasherFactory,
        private MailService $mailService
    ) {
    }

    public function supports($data, array $context = []): bool {
        return $data instanceof ResetPassword;
    }

    public function persist($data, array $context = []): void {
        $observable = $this->dataPersisterObservable
            ->onBeforeCreate(fn ($data) => $this->beforeCreate($data))
            ->onBeforeUpdate(fn ($data) => $this->beforeUpdate($data))
        ;

        $data = $observable->persist($data, $context);
        $this->em->flush();
    }

    public function remove($data, array $context = []) {
        throw new Exception('ResetPasswordDataPersister->remove() is not implemented');
    }

    public function beforeCreate(ResetPassword $data): ResetPassword {
        $user = $this->userRepository->loadUserByIdentifier($data->email);

        if (null != $user) {
            $resetKey = IdGenerator::generateRandomHexString(64);

            $data->id = base64_encode($data->email.'#'.$resetKey);
            $user->passwordResetKeyHash = $this->getResetKeyHasher()->hash($resetKey);

            $this->mailService->sendPasswordResetLink($user, $data);
        }

        return $data;
    }

    public function beforeUpdate(ResetPassword $data): ResetPassword {
        [$email, $resetKey] = explode('#', base64_decode($data->id), 2);
        $user = $this->userRepository->loadUserByIdentifier($email);

        if (
            null == $user
            || null == $user->passwordResetKeyHash
            || !$this->getResetKeyHasher()->verify($user->passwordResetKeyHash, $resetKey)
        ) {
            throw new \Exception('Password reset failed');
        }

        $passwordHasher = $this->pwHasherFactory->getPasswordHasher($user);
        $user->password = $passwordHasher->hash($data->password);
        $user->passwordResetKeyHash = null;

        $data->password = '';

        return $data;
    }

    private function getResetKeyHasher(): PasswordHasherInterface {
        return $this->pwHasherFactory->getPasswordHasher('PasswordResetKey');
    }
}
