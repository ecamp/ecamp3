<?php

namespace EcampLib\Acl;

interface ResourceFactoryInterface
{
    public function createResource($resource);
}
