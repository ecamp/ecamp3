<?php
namespace EcampApi\Resource\Camp;

use PhlyRestfully\HalResource;
use PhlyRestfully\Link;
use EcampCore\Entity\Camp;

class CampBriefResource extends HalResource
{
    public function __construct(Camp $camp)
    {
        $object = array(
                "id" => $camp->getId(),
                "name" => $camp->getName(),
                "title" => $camp->getTitle(),
                "start" => ($camp->getStart() == null) ? null : $camp->getStart()->format(\DateTime::ISO8601),
                "end" => ($camp->getEnd() == null) ? null : $camp->getEnd()->format(\DateTime::ISO8601),
                );

        parent::__construct($object, $object['id']);

        $selfLink = new Link('self');
        $selfLink->setRoute('api/camps', array('camp' => $camp->getId()));

        $webLink = new Link('web');
        if ($camp->belongsToUser()) {
            $webLink->setRoute(
                    'web/user-prefix/name+camp',
                    array('user' => $camp->getOwner()->getId(), 'camp' => $camp->getId())
            );
        } else {
            $webLink->setRoute(
                    'web/group-prefix/name+camp',
                    array('group' => $camp->getGroup()->getId(), 'camp' => $camp->getId())
            );
        }

        $this->getLinks()->add($selfLink)
                        ->add($webLink);

    }
}
