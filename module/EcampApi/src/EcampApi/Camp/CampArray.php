<?php

namespace EcampApi\Camp;

use EcampCore\Entity\Camp;

class CampArray
{
    public function toArray($camp)
    {
    
        return array(
            'id' 				=> 	$camp->getId(),
            'name'				=> 	$camp->getName(),
            'title'				=> 	$camp->getTitle(),
        );
    }
}
