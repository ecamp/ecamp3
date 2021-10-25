<?php

namespace App\Tests\Validator\ColumnLayout;

use App\Entity\ContentNode\ColumnLayout;
use App\Validator\ColumnLayout\AssertNoOrphanChildren;
use App\Validator\ColumnLayout\AssertNoOrphanChildrenValidator;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @internal
 */
class AssertNoOrphanChildrenValidatorTest extends ConstraintValidatorTestCase {
    private const message = 'The following slots still have child contents and should be present in the columns: {{ slots }}';

    private MockObject|RequestStack $requestStack;

    public function testExpectsMatchingAnnotation() {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate(null, new Email());
    }

    public function testExpectsObjectOfTypeColumnLayout() {
        // given
        $this->setObject(new \stdClass());

        // then
        $this->expectException(\InvalidArgumentException::class);

        // when
        $this->validator->validate(null, new AssertNoOrphanChildren());
    }

    public function testNullThrowsException() {
        // given
        $this->setObject(new ColumnLayout());

        // then
        $this->expectException(\TypeError::class);

        // when
        $this->validator->validate(null, new AssertNoOrphanChildren());
    }

    public function testValid() {
        // given
        $testObject = new ColumnLayout();

        $child1 = new ColumnLayout();
        $child1->slot = '1';
        $testObject->addChild($child1);

        $child2 = new ColumnLayout();
        $child2->slot = '2';
        $testObject->addChild($child2);

        $this->setObject($testObject);

        // when
        $this->validator->validate([
            ['slot' => '1'],
            ['slot' => '2'],
        ], new AssertNoOrphanChildren());

        // then
        $this->assertNoViolation();
    }

    public function testInvalid() {
        // given
        $testObject = new ColumnLayout();

        $child1 = new ColumnLayout();
        $child1->slot = 'missingSlot1';
        $testObject->addChild($child1);

        $child2 = new ColumnLayout();
        $child2->slot = 'missingSlot2';
        $testObject->addChild($child2);

        $this->setObject($testObject);

        // when
        $this->validator->validate([
            ['slot' => 'anotherSlot'],
        ], new AssertNoOrphanChildren());

        // then
        $this->buildViolation(self::message)->setParameter('{{ slots }}', 'missingSlot1, missingSlot2')->assertRaised();
    }

    public function testIgnoreMissingSlots() {
        // given
        $testObject = new ColumnLayout();

        $child1 = new ColumnLayout();
        $child1->slot = '1';
        $testObject->addChild($child1);

        $this->setObject($testObject);

        // when
        $this->validator->validate([
            ['slot' => '1'],
            ['anotherKey' => 'anotherValue'],
        ], new AssertNoOrphanChildren());

        // then
        $this->assertNoViolation();
    }

    protected function createValidator() {
        $this->requestStack = $this->createMock(RequestStack::class);

        return new AssertNoOrphanChildrenValidator($this->requestStack);
    }
}
