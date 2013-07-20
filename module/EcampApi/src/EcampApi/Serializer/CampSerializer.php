<?php

namespace EcampApi\Serializer;

use EcampCore\Entity\Camp;

class CampSerializer extends BaseSerializer
{
    protected function serialize($camp)
    {
        $userSerializer = new UserSerializer($this->format, $this->router);
        $periodSerializer = new PeriodSerializer($this->format, $this->router);
        $eventSerializer = new EventSerializer($this->format, $this->router);
        $collaborationSerializer = new CollaboratorSerializer($this->format, $this->router);

        return array(
            'id' 				=> 	$camp->getId(),
            'href'				=>	$this->getCampHref($camp),
            'owner'				=>	$userSerializer->getReference($camp->getOwner()),
            'group'				=> 	null,
            'creator'			=> 	$userSerializer->getReference($camp->getCreator()),
            'name'				=> 	$camp->getName(),
            'title'				=> 	$camp->getTitle(),
            'periods'			=> 	$periodSerializer->getCollectionReference($camp),
            'events'			=>	$eventSerializer->getCollectionReference($camp),
            'collaborations'	=>  $collaborationSerializer->getCollectionReference($camp)
        );
    }

    public function getReference(Camp $camp = null)
    {
        if ($camp == null) {
            return null;
        } else {
            return array(
                'id'	=>	$camp->getId(),
                'href'	=>	$this->getCampHref($camp)
            );
        }
    }

    private function getCampHref(Camp $camp)
    {
        return
            $this->router->assemble(
                array(
                    'controller' => 'camps',
                    'action' => 'get',
                    'id' => $camp->getId(),
                    'format' => $this->format
                ),
                array(
                    'name' => 'api/rest',
                )
            );
    }
}
