<?php

namespace EcampCore\Job;

use EcampLib\Job\AbstractJobFactory;
use EcampLib\Job\JobInterface;

class SendActivationMailJobFactory
    extends AbstractJobFactory
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

        return new SendActivationMailJob($user);
    }
}
