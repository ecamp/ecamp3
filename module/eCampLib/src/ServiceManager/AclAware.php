<?php

namespace eCamp\Lib\ServiceManager;

use eCamp\Lib\Acl\Acl;

interface AclAware
{
    public function setAcl(Acl $acl);
}
