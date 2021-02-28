<?php

namespace eCamp\LibTest\Acl;

use eCamp\Lib\Acl\AclAssertion;
use eCamp\LibTest\PHPUnit\AbstractTestCase;
use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Assertion\AssertionInterface;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

/**
 * @internal
 */
class AclAssertionTest extends AbstractTestCase {
    private Acl $acl;
    private AssertionInterface $true;
    private AssertionInterface $false;

    public function setUp(): void {
        parent::setUp();

        $this->acl = new Acl();
        $this->true = new AssertionMock(true);
        $this->false = new AssertionMock(false);
    }

    public function testAndAssertion(): void {
        $this->assertTrue(AclAssertion::and($this->true, $this->true)->assert($this->acl));
        $this->assertFalse(AclAssertion::and($this->true, $this->false)->assert($this->acl));
        $this->assertFalse(AclAssertion::and($this->false, $this->false)->assert($this->acl));
    }

    public function testOrAssertion(): void {
        $this->assertTrue(AclAssertion::or($this->true, $this->true)->assert($this->acl));
        $this->assertTrue(AclAssertion::or($this->true, $this->false)->assert($this->acl));
        $this->assertFalse(AclAssertion::or($this->false, $this->false)->assert($this->acl));
    }
}

class AssertionMock implements AssertionInterface {
    private bool $result;

    public function __construct(bool $result) {
        $this->result = $result;
    }

    public function assert(Acl $acl, RoleInterface $role = null, ResourceInterface $resource = null, $privilege = null) {
        return $this->result;
    }
}
