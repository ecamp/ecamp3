<?php

namespace App\DataPersister;

use App\DataPersister\Util\AbstractDataPersister;
use App\DataPersister\Util\CustomActionListener;
use App\DataPersister\Util\DataPersisterObservable;
use App\Entity\BaseEntity;
use App\Entity\User;
use App\Security\ReCaptcha\ReCaptcha;
use App\Service\MailService;
use App\Util\IdGenerator;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserDataPersister extends AbstractDataPersister {
    /**
     * @throws \ReflectionException
     */
    public function __construct(
        DataPersisterObservable $dataPersisterObservable,
        private ReCaptcha $reCaptcha,
        private UserPasswordHasherInterface $userPasswordHasher,
        private MailService $mailService
    ) {
        $onActivateListener = CustomActionListener::of(User::ACTIVATE, beforeAction: fn ($data) => $this->onActivate($data));
        parent::__construct(
            User::class,
            $dataPersisterObservable,
            customActionListeners: [$onActivateListener]
        );
    }

    /**
     * @param User $data
     *
     * @return User
     */
    public function beforeCreate($data) {
        $resp = $this->reCaptcha->verify($data->recaptchaToken);
        if (!$resp->isSuccess()) {
            throw new HttpException(422, 'ReCaptcha failed');
        }

        $data->state = User::STATE_REGISTERED;
        if ($data->plainPassword) {
            $data->password = $this->userPasswordHasher->hashPassword($data, $data->plainPassword);
            $data->eraseCredentials();
        }
        $data->activationKey = IdGenerator::generateRandomHexString(64);
        $data->activationKeyHash = md5($data->activationKey);

        return $data;
    }

    public function afterCreate($data): void {
        $this->mailService->sendUserActivationMail($data, $data->activationKey);
    }

    /**
     * @param User $data
     *
     * @return User
     */
    public function beforeUpdate($data) {
        if ($data->plainPassword) {
            $data->password = $this->userPasswordHasher->hashPassword($data, $data->plainPassword);
            $data->eraseCredentials();
        }

        return $data;
    }

    /**
     * @throws \Exception
     */
    public function onActivate($data): BaseEntity {
        if ($data->activationKeyHash === md5($data->activationKey)) {
            $data->state = User::STATE_ACTIVATED;
            $data->activationKey = null;
            $data->activationKeyHash = null;
        } else {
            throw new \Exception('Invalid ActivationKey');
        }

        return $data;
    }
}
