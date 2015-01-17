<?php
namespace EcampMaterial\Resource\MaterialItem;

use EcampMaterial\Entity\MaterialItem;

use PhlyRestfully\HalResource;
use PhlyRestfully\Link;

class MaterialItemBriefResource extends HalResource
{
    public function __construct(MaterialItem $entity)
    {
        $object = array(
                'id'				=> 	$entity->getId(),
                'quantity'			=> 	$entity->getQuantity(),
                'text'				=> 	$entity->getText()
                );

        parent::__construct($object, $object['id']);

        $selfLink = new Link('self');
        $selfLink->setRoute('api-material/items', array('item' => $entity->getId()));

        $this->getLinks()->add($selfLink);

    }
}
