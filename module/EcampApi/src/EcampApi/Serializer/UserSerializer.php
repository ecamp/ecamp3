<?php

namespace EcampApi\Serializer;

use EcampCore\Entity\User;

class UserSerializer extends BaseSerializer
{
    public function serialize($user)
    {
        return array(
            'id'		=> 	$user->getId(),
            'href'		=>	$this->getUserHref($user),
            'username'	=>	$user->getUsername(),
            'email'		=>	$user->getEmail(),
            'scoutname'	=>	$user->getScoutname(),
            'firstname'	=>	$user->getFirstname(),
            'surname'	=>	$user->getSurname()
        );
    }

    public function getReference(User $user = null)
    {
        if ($user == null) {
            return null;
        } else {
            return array(
                'id'	=>	$user->getId(),
                'href'	=>	$this->getUserHref($user)
            );
        }
    }

    private function getUserHref(User $user)
    {
        return
            $this->router->assemble(
                array(
                    'controller' => 'users',
                    'action' => 'get',
                    'id' => $user->getId(),
                    'format' => $this->format
                ),
                array(
                    'name' => 'api/default'
                )
            );
    }

}
