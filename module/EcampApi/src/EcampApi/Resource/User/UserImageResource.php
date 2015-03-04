<?php

namespace EcampApi\Resource\User;

use EcampCore\Entity\Image;
use EcampCore\Entity\User;
use PhlyRestfully\HalResource;
use PhlyRestfully\Link;

class UserImageResource extends HalResource
{
    public function __construct(User $user, Image $image)
    {
        $object = array(
            'id'    =>  $image->getId(),
            'mime'  => 	$image->getMime(),
            'size'  =>	$image->getSize(),
        );

        parent::__construct($object, $user->getId());

        $selfLink = new Link('self');
        $selfLink->setRoute('api/users/image', array('user' => $user));

        $this->getLinks()
            ->add($selfLink)
        ;

    }
}
