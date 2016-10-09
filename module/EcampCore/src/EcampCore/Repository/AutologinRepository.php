<?php

namespace EcampCore\Repository;

use Doctrine\ORM\EntityRepository;
use EcampCore\Entity\Autologin;

/**
 * Class AutologinRepository
 * @package EcampCore\Repository
 *
 * @method Autologin find($id)
 */
class AutologinRepository extends EntityRepository
{

    public function findByToken($token)
    {
        $autologinToken = Autologin::GetHash($token);

        return $this->findOneBy(array('autologinToken' => $autologinToken));
    }

}
