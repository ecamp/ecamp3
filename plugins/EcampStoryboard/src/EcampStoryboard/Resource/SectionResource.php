<?php
namespace EcampStoryboard\Resource;

use EcampStoryboard\Entity\Section;
use PhlyRestfully\HalResource;
use PhlyRestfully\Link;

class SectionResource extends HalResource
{
    public function __construct(Section $entity)
    {
        $object = array(
            'id'        => $entity->getId(),
            'position'  => $entity->getPosition(),
            'duration'  => $entity->getDurationInMinutes(),
            'text'      => $entity->getText(),
            'info'      => $entity->getInfo()
        );

        parent::__construct($object, $object['id']);

        $selfLink = new Link('self');
        $selfLink->setRoute('plugin/storyboard/api', array('section' => $entity->getId()));

        $this->getLinks()->add($selfLink);
    }

}
