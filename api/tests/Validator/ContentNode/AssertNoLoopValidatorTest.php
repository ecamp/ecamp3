<?php

namespace App\Tests\Validator\ContentNode;

use App\Entity\ContentNode;
use App\Validator\ContentNode\AssertNoLoop;
use App\Validator\ContentNode\AssertNoLoopValidator;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @internal
 */
class AssertNoLoopValidatorTest extends ConstraintValidatorTestCase {
    public function testExpectsMatchingAnnotation() {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate(null, new Email());
    }

    public function testNullIsValid() {
        $this->validator->validate(null, new AssertNoLoop());
        $this->assertNoViolation();
    }

    public function testEmptyIsValid() {
        $this->validator->validate('', new AssertNoLoop());
        $this->assertNoViolation();
    }

    public function testValid() {
        // given
        $object = new IdSettableContentNode('1');
        $parent = new IdSettableContentNode('2');
        $object->parent = $parent;
        $this->setObject($object);

        // when
        $this->validator->validate($parent, new AssertNoLoop());

        // then
        $this->assertNoViolation();
    }

    public function testLoopInvalid() {
        // given
        $object = new IdSettableContentNode('1');
        $parent = new IdSettableContentNode('2');
        $object->parent = $parent;
        $parent->parent = $object;
        $this->setObject($object);

        // when
        $this->validator->validate($parent, new AssertNoLoop());

        // then
        $this->buildViolation('Must not form a loop of parent-child relations.')->assertRaised();
    }

    public function testSelfLoopInvalid() {
        // given
        $object = new IdSettableContentNode('1');
        $object->parent = $object;
        $this->setObject($object);

        // when
        $this->validator->validate($object, new AssertNoLoop());

        // then
        $this->buildViolation('Must not form a loop of parent-child relations.')->assertRaised();
    }

    public function testAncestorSelfLoopIsIgnored() {
        // given
        $object = new IdSettableContentNode('1');
        $parent = new IdSettableContentNode('2');
        $object->parent = $parent;
        $parent->parent = $parent;
        $this->setObject($object);

        // when
        $this->validator->validate($parent, new AssertNoLoop());

        // then
        $this->assertNoViolation();
    }

    public function testAncestorLoopIsIgnored() {
        // given
        $object = new IdSettableContentNode('1');
        $parent = new IdSettableContentNode('2');
        $grandparent = new IdSettableContentNode('3');
        $object->parent = $parent;
        $parent->parent = $grandparent;
        $grandparent->parent = $parent;
        $this->setObject($object);

        // when
        $this->validator->validate($parent, new AssertNoLoop());

        // then
        $this->assertNoViolation();
    }

    protected function createValidator(): ConstraintValidatorInterface {
        return new AssertNoLoopValidator();
    }
}

class IdSettableContentNode extends ContentNode {
    public function __construct($id) {
        parent::__construct();
        $this->id = $id;
    }
}
