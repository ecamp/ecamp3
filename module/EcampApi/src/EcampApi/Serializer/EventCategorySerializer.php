<?php

namespace EcampApi\Serializer;

use EcampCore\Entity\Camp;
use EcampCore\Entity\EventCategory;

class EventCategorySerializer extends BaseSerializer
{
    public function serialize($eventCategory)
    {
        /* @var $eventCategory \EcampCore\Entity\EventCategory */

        $campSerializer = new CampSerializer($this->format, $this->router);

        return array(
            'id' 		=> 	$eventCategory->getId(),
            'href'		=>	$this->getEventCategoryHref($eventCategory),
            'name'		=>	$eventCategory->getName(),
            'color'		=>	$eventCategory->getColor(),
            'numbering'	=>	$eventCategory->getNumberingStyle(),
            'camp'		=>  $campSerializer->getReference($eventCategory->getCamp()),
        );
    }

    public function getReference(EventCategory $eventCategory = null)
    {
        if ($eventCategory == null) {
            return null;
        } else {
            return array(
                'id'	=>	$eventCategory->getId(),
                'href'	=>	$this->getEventCategoryHref($eventCategory)
            );
        }
    }

    public function getCollectionReference($collectionOwner)
    {
        if ($collectionOwner instanceof Camp) {
            return array('href' => $this->getCamp_EventCategoryCollectionHref($collectionOwner));
        }

        return null;
    }

    private function getEventCategoryHref(EventCategory $eventCategory)
    {
        return
            $this->router->assemble(
                array(
                    'controller' => 'eventCategories',
                    'action' => 'get',
                    'id' => $eventCategory->getId(),
                    'format' => $this->format
                ),
                array(
                    'name' => 'api/rest'
                )
        );
    }

    private function getCamp_EventCategoryCollectionHref(Camp $camp)
    {
        return
            $this->router->assemble(
                array(
                    'controller' => 'eventCategories',
                    'action' => 'getList',
                    'camp' => $camp->getId(),
                    'format' => $this->format
                ),
                array(
                    'name' => 'api/camp/rest'
                )
            );
    }

}
