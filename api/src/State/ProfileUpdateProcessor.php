<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Profile;
use App\Service\MailService;
use App\State\Util\AbstractPersistProcessor;
use App\Util\IdGenerator;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class ProfileUpdateProcessor extends AbstractPersistProcessor {
    public function __construct(
        ProcessorInterface $decorated,
        private PasswordHasherFactoryInterface $pwHasherFactory,
        private MailService $mailService
    ) {
        parent::__construct($decorated);
    }

    /**
     * @param Profile $data
     */
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): Profile {
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

    public function onAfter($data, Operation $operation, array $uriVariables = [], array $context = []): void {
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
