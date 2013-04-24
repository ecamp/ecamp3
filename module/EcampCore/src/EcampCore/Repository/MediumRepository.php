<?php

namespace EcampCore\Repository;

use Doctrine\ORM\EntityRepository;

class MediumRepository 
	extends EntityRepository
{
	
	public function getDefaultMedium(){
		return $this->findOneBy(array('default' => true));
	}
	
}
