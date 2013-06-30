<?php

namespace EcampCoreTest\Mock;

use EcampLib\Acl\Acl;

class AclMock extends Acl
{

    public function isAllowed($role = null, $resource = null, $privilege = null)
    {
        return true;
    }

}
