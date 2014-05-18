<?php

namespace EcampCore\Repository;

use Doctrine\ORM\EntityRepository;
use EcampCore\Entity\AutoLogin;

class AutoLoginRepository extends EntityRepository
{

    public function findByToken($token)
    {
        $autologinToken = AutoLogin::GetHash($token);

        return $this->findOneBy(array('autologinToken' => $autologinToken));
    }

}
