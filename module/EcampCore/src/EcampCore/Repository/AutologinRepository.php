<?php

namespace EcampCore\Repository;

use Doctrine\ORM\EntityRepository;
use EcampCore\Entity\Autologin;

class AutologinRepository extends EntityRepository
{

    public function findByToken($token)
    {
        $autologinToken = Autologin::GetHash($token);

        return $this->findOneBy(array('autologinToken' => $autologinToken));
    }

}
