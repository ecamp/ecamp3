<?php

namespace App\Tests\Validator\ContentNode;

use App\Entity\ContentNode\ColumnLayout;
use App\Entity\ContentNode\DefaultLayout;
use App\Validator\ContentNode\AssertAttachedToRoot;
use App\Validator\ContentNode\AssertAttachedToRootValidator;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @internal
 */
class AssertAttachedToRootValidatorTest extends ConstraintValidatorTestCase {
    public function testExpectsMatchingAnnotation() {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate(null, new Email());
    }

    public function testNullIsValid() {
        $this->validator->validate(null, new AssertAttachedToRoot());
        $this->assertNoViolation();
    }

    public function testEmptyIsValid() {
        $this->validator->validate('', new AssertAttachedToRoot());
        $this->assertNoViolation();
    }

    public function testValid() {
        // given
        $root = new ColumnLayout();
        $root->root = $root;
        $parent = new DefaultLayout();
        $parent->root = $root;
        $parent->parent = $root;
        $this->setObject($parent);

        // when
        $this->validator->validate($root, new AssertAttachedToRoot());

        // then
        $this->assertNoViolation();
    }

    public function testNotDirectlyAttachedInvalid() {
        // given
        $root = new ColumnLayout();
        $root->root = $root;
        $parent = new ColumnLayout();
        $parent->root = $root;
        $parent->parent = $root;
        $object = new DefaultLayout();
        $object->root = $root;
        $object->parent = $parent;
        $this->setObject($object);

        // when
        $this->validator->validate($parent, new AssertAttachedToRoot());

        // then
        $this->buildViolation('Must be attached to the root layout.')->assertRaised();
    }

    public function testAppliesOnlyToDefaultLayout() {
        // given
        $root = new ColumnLayout();
        $root->root = $root;
        $parent = new ColumnLayout();
        $parent->root = $root;
        $parent->parent = $root;
        $object = new ColumnLayout();
        $object->root = $root;
        $object->parent = $parent;
        $this->setObject($object);

        // when
        $this->validator->validate($parent, new AssertAttachedToRoot());

        // then
        $this->assertNoViolation();
    }

    public function testRootLayoutNullParentIsIgnored() {
        // given
        $root = new ColumnLayout();
        $root->root = $root;
        $object = new ColumnLayout();
        $object->root = $root;
        $this->setObject($object);

        // when
        $this->validator->validate($root, new AssertAttachedToRoot());

        // then
        $this->assertNoViolation();
    }

    protected function createValidator(): ConstraintValidatorInterface {
        return new AssertAttachedToRootValidator();
    }
}
