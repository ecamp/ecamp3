<?php

namespace EcampCore\Job\Mail;

use EcampLib\Job\AbstractJobFactory;
use EcampLib\Job\JobInterface;

class SendEmailVerificationEmailJobFactory extends AbstractJobFactory
{
    /** @return \EcampCore\Repository\UserRepository */
    protected function getUserRepository()
    {
        return $this->getService('EcampCore\Repository\User');
    }

    /**
     * @param  array        $options
     * @return JobInterface
     */
    public function create($options = null)
    {
        $userId = $options['userId'];
        /** @var \EcampCore\Entity\User $user */
        $user = $this->getUserRepository()->find($userId);

        return new SendEmailVerificationEmailJob($user);
    }
}