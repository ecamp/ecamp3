<?php
namespace EcampMaterial\Resource\MaterialList;

use EcampMaterial\Entity\MaterialList;

use PhlyRestfully\HalResource;
use PhlyRestfully\Link;

class MaterialListBriefResource extends HalResource
{
    public function __construct(MaterialList $entity)
    {
        $object = array(
                'id'				=> 	$entity->getId(),
                'name'				=> 	$entity->getName()
                );

        parent::__construct($object, $object['id']);

        $selfLink = new Link('self');
        $selfLink->setRoute('api-material/lists', array('list' => $entity->getId()));

        $this->getLinks()->add($selfLink);

    }
}
