<?php

namespace EcampCore\Repository;

use Doctrine\ORM\EntityRepository;
use EcampCore\Entity\Medium;

/**
 * Class MediumRepository
 * @package EcampCore\Repository
 *
 * @method Medium find($id)
 */
class MediumRepository
    extends EntityRepository
{
    /**
     * @return Medium
     */
    public function getDefaultMedium()
    {
        return $this->findOneBy(array('default' => true));
    }

}
