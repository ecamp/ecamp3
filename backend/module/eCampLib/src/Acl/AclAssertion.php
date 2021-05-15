<?php

namespace eCamp\Lib\Acl;

use Exception;
use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Assertion\AssertionInterface;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Laminas\Permissions\Acl\Role\RoleInterface;

class AclAssertion implements AssertionInterface {
    public const OR = 'OR';
    public const AND = 'AND';

    private $operand;
    private $assertions = [];

    public static function or(AssertionInterface ...$assertions): AclAssertion {
        $assertion = new AclAssertion();
        $assertion->operand = self::OR;
        $assertion->assertions = $assertions;

        return $assertion;
    }

    public static function and(AssertionInterface ...$assertions): AclAssertion {
        $assertion = new AclAssertion();
        $assertion->operand = self::AND;
        $assertion->assertions = $assertions;

        return $assertion;
    }

    public function assert(Acl $acl, RoleInterface $role = null, ResourceInterface $resource = null, $privilege = null): bool {
        if (self::OR == $this->operand) {
            for ($i = 0; $i < count($this->assertions); ++$i) {
                /** @var AssertionInterface $assertion */
                $assertion = $this->assertions[$i];
                if ($assertion->assert($acl, $role, $resource, $privilege)) {
                    return true;
                }
            }

            return false;
        }
        if (self::AND == $this->operand) {
            for ($i = 0; $i < count($this->assertions); ++$i) {
                /** @var AssertionInterface $assertion */
                $assertion = $this->assertions[$i];
                if (!$assertion->assert($acl, $role, $resource, $privilege)) {
                    return false;
                }
            }

            return true;
        }

        throw new Exception('Not implemented operand: '.$this->operand);
    }
}
