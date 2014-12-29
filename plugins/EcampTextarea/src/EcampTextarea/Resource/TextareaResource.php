<?php

namespace EcampTextarea\Resource;

use EcampTextarea\Entity\Textarea;
use PhlyRestfully\HalResource;
use PhlyRestfully\Link;

class TextareaResource extends HalResource
{

    public function __construct(Textarea $entity)
    {
        $object = array(
            'id'        => $entity->getId(),
            'text'      => $entity->getText(),
        );

        parent::__construct($object, $object['id']);

        $selfLink = new Link('self');
        $selfLink->setRoute('plugin/textarea/api', array('textarea' => $entity->getId()));

        $this->getLinks()->add($selfLink);
    }

}