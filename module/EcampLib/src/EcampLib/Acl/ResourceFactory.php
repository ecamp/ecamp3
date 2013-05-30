<?php

namespace EcampLib\Acl;

class ResourceFactory
	implements ResourceFactoryInterface
{
	public function createResource($resource){
		return $resource;
	}
}