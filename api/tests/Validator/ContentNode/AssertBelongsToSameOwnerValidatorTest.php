<?php

namespace App\Tests\Validator\ContentNode;

use App\Entity\Activity;
use App\Entity\BaseEntity;
use App\Entity\BelongsToCampInterface;
use App\Entity\Camp;
use App\Entity\Category;
use App\Entity\ContentNode\ColumnLayout;
use App\Validator\ContentNode\AssertBelongsToSameOwner;
use App\Validator\ContentNode\AssertBelongsToSameOwnerValidator;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @internal
 */
class AssertBelongsToSameOwnerValidatorTest extends ConstraintValidatorTestCase {
    public function testExpectsMatchingAnnotation() {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate(null, new Email());
    }

    public function testExpectsContentNodeValue() {
        $this->expectException(UnexpectedValueException::class);
        $this->validator->validate(new \stdClass(), new AssertBelongsToSameOwner());
    }

    public function testExpectsContentNodeObject() {
        // given
        $camp = $this->createMock(Camp::class);
        $camp->method('getId')->willReturn('idfromtest');
        $child = new ChildTestClass($camp);

        $this->setObject(new \stdClass());

        // then
        $this->expectException(UnexpectedValueException::class);

        // when
        $this->validator->validate($child, new AssertBelongsToSameOwner());
    }

    public function testNullIsValid() {
        $this->validator->validate(null, new AssertBelongsToSameOwner());
        $this->assertNoViolation();
    }

    public function testEmptyIsValid() {
        $this->validator->validate('', new AssertBelongsToSameOwner());
        $this->assertNoViolation();
    }

    public function testValid() {
        // given
        $activity = $this->createMock(Activity::class);
        $activity->method('getId')->willReturn('idfromtest');
        $root = new ColumnLayout();
        $root->owner = $activity;
        $root->root = $root;
        $parent = new ColumnLayout();
        $parent->root = $root;
        $parent->parent = $root;
        $child = new ColumnLayout();
        $child->root = $root;
        $child->parent = $root;
        $this->setObject($child);

        // when
        $this->validator->validate($parent, new AssertBelongsToSameOwner());

        // then
        $this->assertNoViolation();
    }

    public function testInvalid() {
        // given
        $activity = $this->createMock(Activity::class);
        $activity->method('getId')->willReturn('idfromtest');
        $category = $this->createMock(Category::class);
        $category->method('getId')->willReturn('anotheridfromtest');
        $root = new ColumnLayout();
        $root->owner = $activity;
        $root->root = $root;
        $root2 = new ColumnLayout();
        $root2->owner = $category;
        $root2->root = $root2;
        $parent = new ColumnLayout();
        $parent->root = $root2;
        $parent->parent = $root2;
        $child = new ColumnLayout();
        $child->root = $root;
        $child->parent = $root;
        $this->setObject($child);

        // when
        $this->validator->validate($parent, new AssertBelongsToSameOwner());

        // then
        $this->buildViolation('Must belong to the same owner.')->assertRaised();
    }

    protected function createValidator() {
        return new AssertBelongsToSameOwnerValidator();
    }
}

class ChildTestClass implements BelongsToCampInterface {
    public function __construct(public Camp $camp) {
    }

    public function getCamp(): ?Camp {
        return $this->camp;
    }
}

class ParentTestClass extends BaseEntity implements BelongsToCampInterface {
    #[AssertBelongsToSameOwner]
    public ChildTestClass $child;

    public function __construct(public Camp $camp, ChildTestClass $child) {
    }

    public function getCamp(): ?Camp {
        return $this->camp;
    }
}
