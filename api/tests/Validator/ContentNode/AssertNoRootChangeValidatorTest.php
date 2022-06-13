<?php

namespace App\Tests\Validator\ContentNode;

use App\Entity\BaseEntity;
use App\Entity\BelongsToCampInterface;
use App\Entity\Camp;
use App\Entity\Category;
use App\Entity\ContentNode\ColumnLayout;
use App\Validator\ContentNode\AssertNoRootChange;
use App\Validator\ContentNode\AssertNoRootChangeValidator;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @internal
 */
class AssertNoRootChangeValidatorTest extends ConstraintValidatorTestCase {
    private MockObject|RequestStack $requestStack;

    public function testExpectsMatchingAnnotation() {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate(null, new Email());
    }

    public function testExpectsContentNodeValue() {
        $this->expectException(UnexpectedValueException::class);
        $this->validator->validate(new \stdClass(), new AssertNoRootChange());
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
        $this->validator->validate($child, new AssertNoRootChange());
    }

    public function testNullIsNotValid() {
        // then
        $this->expectException(UnexpectedValueException::class);

        // when
        $this->validator->validate(null, new AssertNoRootChange());
    }

    public function testNullIsValidIfValueWasNullBefore() {
        // given
        $previous = new ColumnLayout();
        $previous->parent = null;

        $current = new ColumnLayout();
        $current->parent = null;
        $this->setProperty($current, 'parent');

        $request = $this->createMock(Request::class);
        $request->attributes = new ParameterBag(['previous_data' => $previous]);
        $this->requestStack->method('getCurrentRequest')->willReturn($request);

        // when
        $this->validator->validate($current->parent, new AssertNoRootChange());

        // then
        $this->assertNoViolation();
    }

    public function testEmptyIsNotValid() {
        // then
        $this->expectException(UnexpectedValueException::class);

        // when
        $this->validator->validate('', new AssertNoRootChange());
    }

    public function testValid() {
        // given
        $root = new ColumnLayout();
        $root->root = $root;
        $parent = new ColumnLayout();
        $parent->root = $root;
        $parent->parent = $root;
        $child = new ColumnLayout();
        $child->root = $root;
        $child->parent = $root;
        $this->setObject($child);

        // when
        $this->validator->validate($parent, new AssertNoRootChange());

        // then
        $this->assertNoViolation();
    }

    public function testInvalid() {
        // given
        $category = $this->createMock(Category::class);
        $category->method('getId')->willReturn('anotheridfromtest');
        $root = new ColumnLayout();
        $root->root = $root;
        $root2 = new ColumnLayout();
        $root2->root = $root2;
        $parent = new ColumnLayout();
        $parent->root = $root2;
        $parent->parent = $root2;
        $child = new ColumnLayout();
        $child->root = $root;
        $child->parent = $root;
        $this->setObject($child);

        // when
        $this->validator->validate($parent, new AssertNoRootChange());

        // then
        $this->buildViolation('Must belong to the same root.')->assertRaised();
    }

    protected function createValidator() {
        $this->requestStack = $this->createMock(RequestStack::class);

        return new AssertNoRootChangeValidator($this->requestStack);
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
    #[AssertNoRootChange]
    public ChildTestClass $child;

    public function __construct(public Camp $camp, ChildTestClass $child) {
    }

    public function getCamp(): ?Camp {
        return $this->camp;
    }
}
