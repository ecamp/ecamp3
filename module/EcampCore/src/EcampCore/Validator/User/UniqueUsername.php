<?php

namespace EcampCore\Validator\User;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Validator\NoObjectExists;

class UniqueUsername extends NoObjectExists
{
    public function __construct(ObjectManager $objectManager)
    {
        $this->messageTemplates[self::ERROR_OBJECT_FOUND] = "Username already taken";

        parent::__construct(array(
            'object_repository' => $objectManager->getRepository('EcampCore\Entity\User'),
            'fields' => array('username')
        ));

    }

}
