<?php

namespace eCamp\LibTest\Acl;

use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class AclTest extends AbstractTestCase {
    public function testIsAllowed(): void {
        /** @var Acl $acl */
        $acl = \eCampApp::GetService(Acl::class);

        $this->assertFalse($acl->isAllowed());
    }

    public function testAssertAllowed(): void {
        /** @var Acl $acl */
        $acl = \eCampApp::GetService(Acl::class);

        $this->expectException(NoAccessException::class);
        $acl->assertAllowed();
    }
}
