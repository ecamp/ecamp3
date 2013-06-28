<?php

namespace EcampApi\Serializer;

use EcampCore\Entity\User;
use EcampCore\Entity\UserCamp;

class ContributorSerializer extends BaseSerializer
{
    public function serialize($uc)
    {
        $userSerializer = new UserSerializer($this->format, $this->router);
        $campSerializer = new CampSerializer($this->format, $this->router);

        return array(
            'id'		=> 	$uc->getId(),
            'href'		=>	$this->getContributorHref($uc),
            'user'		=>	$userSerializer->getReference($uc->getUser()),
            'camp'		=>	$campSerializer->getReference($uc->getCamp()),
            'role'		=>	$uc->getRole()
        );
    }

    private function getContributorHref(UserCamp $userCamp)
    {
        return
            $this->router->assemble(
                array(
                    'controller' => 'contributor',
                    'action' => 'get',
                    'id' => $userCamp->getId(),
                    'format' => $this->format
                ),
                array(
                    'name' => 'api/default'
                )
            );
    }
}
