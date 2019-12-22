<?php

namespace eCamp\LibTest\Acl;

use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

class AclTest extends AbstractTestCase {
    public function testIsAllowed() {
        /** @var Acl $acl */
        $acl = \eCampApp::GetService(Acl::class);

        $this->assertFalse($acl->isAllowed());
    }

    public function testAssertAllowed() {
        /** @var Acl $acl */
        $acl = \eCampApp::GetService(Acl::class);

        $this->expectException(NoAccessException::class);
        $acl->assertAllowed();
    }
}
