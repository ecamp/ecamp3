<?php

namespace App\DataPersister;

use App\DataPersister\Util\AbstractDataPersister;
use App\DataPersister\Util\DataPersisterObservable;
use App\Entity\Profile;
use App\Service\MailService;
use App\Util\IdGenerator;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class ProfileDataPersister extends AbstractDataPersister {
    /**
     * @throws \ReflectionException
     */
    public function __construct(
        DataPersisterObservable $dataPersisterObservable,
        private PasswordHasherFactoryInterface $pwHasherFactory,
        private MailService $mailService
    ) {
        parent::__construct(
            Profile::class,
            $dataPersisterObservable,
        );
    }

    /**
     * @param Profile $data
     */
    public function beforeUpdate($data): Profile {
        /** @var Profile $data */
        if (isset($data->newEmail)) {
            $verificationKey = IdGenerator::generateRandomHexString(64);
            $data->untrustedEmail = $data->newEmail;
            $data->untrustedEmailKey = $verificationKey;
            $data->untrustedEmailKeyHash = $this->getResetKeyHasher()->hash($verificationKey);
        } elseif (isset($data->untrustedEmailKey)) {
            if (!isset($data->untrustedEmailKeyHash)) {
                throw new HttpException(422, 'Email verification failed A');
            }

            if (!$this->getResetKeyHasher()->verify($data->untrustedEmailKeyHash, $data->untrustedEmailKey)) {
                throw new HttpException(422, 'Email verification failed B');
            }

            $data->email = $data->untrustedEmail;
            $data->untrustedEmail = null;
            $data->untrustedEmailKey = null;
            $data->untrustedEmailKeyHash = null;
        }

        return $data;
    }

    public function afterUpdate($data): void {
        /** @var Profile $data */
        if (isset($data->untrustedEmailKey)) {
            $this->mailService->sendEmailVerificationMail($data->user, $data);
            $data->untrustedEmailKey = null;
        }
    }

    private function getResetKeyHasher(): PasswordHasherInterface {
        return $this->pwHasherFactory->getPasswordHasher('EmailVerification');
    }
}
