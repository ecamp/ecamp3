<?php

namespace EcampApi\Resource\Image;

use EcampCore\Entity\Image;
use PhlyRestfully\HalResource;
use PhlyRestfully\Link;

class ImageResource extends HalResource
{
    public function __construct(Image $image)
    {
        $object = array(
            'id'    =>  $image->getId(),
            'mime'  => 	$image->getMime(),
            'size'  =>	$image->getSize(),
        );

        parent::__construct($object, $object['id']);

        $selfLink = new Link('self');
        $selfLink->setRoute('api/images', array('image' => $image->getId()));
    }
}