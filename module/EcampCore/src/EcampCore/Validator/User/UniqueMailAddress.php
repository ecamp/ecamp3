<?php

namespace EcampCore\Validator\User;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Validator\NoObjectExists;

class UniqueMailAddress extends NoObjectExists
{
    public function __construct(ObjectManager $objectManager)
    {
        $this->messageTemplates[self::ERROR_OBJECT_FOUND] = "eMail address already registered";

        parent::__construct(array(
            'object_repository' => $objectManager->getRepository('EcampCore\Entity\User'),
            'fields' => array('email')
        ));

    }

}
