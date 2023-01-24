<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\State\Util\AbstractPersistProcessor;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserActivateProcessor extends AbstractPersistProcessor {
    public function __construct(
        ProcessorInterface $decorated
    ) {
        parent::__construct($decorated);
    }

    /**
     * @param User $data
     */
    public function onBefore($data, Operation $operation, array $uriVariables = [], array $context = []): User {
        if ($data->activationKeyHash === md5($data->activationKey)) {
            $data->state = User::STATE_ACTIVATED;
            $data->activationKey = null;
            $data->activationKeyHash = null;
        } else {
            throw new HttpException(422, 'Invalid ActivationKey');
        }

        return $data;
    }
}
