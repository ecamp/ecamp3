<?php

namespace App\Tests\Validator\ContentNode;

use App\Entity\Camp;
use App\Entity\ContentNode\ColumnLayout;
use App\Entity\ContentNode\SingleText;
use App\Validator\ContentNode\AssertSlotSupportedByParent;
use App\Validator\ContentNode\AssertSlotSupportedByParentValidator;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @internal
 */
class AssertSlotSupportedByParentValidatorTest extends ConstraintValidatorTestCase {
    public const MESSAGE = 'message {{ supportedSlotNames }} {{ value }}';
    private ColumnLayout $parent;

    public function testAcceptsValidValue() {
        $this->validator->validate('1', new AssertSlotSupportedByParent());

        $this->assertNoViolation();
    }

    public function testRejectsInvalidValue() {
        $slotName = 'invalidSlot';
        $this->validator->validate($slotName, new AssertSlotSupportedByParent());

        $this->buildViolation(AssertSlotSupportedByParent::MESSAGE)
            ->setParameter('{{ supportedSlotNames }}', join(',', $this->parent->getSupportedSlotNames()))
            ->setParameter('{{ value }}', $slotName)
            ->assertRaised()
        ;
    }

    public function testAcceptsNullIfParentIsNull() {
        $this->context->getObject()->parent = null;
        $this->validator->validate(null, new AssertSlotSupportedByParent());

        $this->assertNoViolation();
    }

    public function testRejectsSlotIfParentIsNull() {
        $this->context->getObject()->parent = null;
        $this->validator->validate('1', new AssertSlotSupportedByParent());

        $this->buildViolation(AssertSlotSupportedByParent::NO_PARENT_MESSAGE)
            ->assertRaised()
        ;
    }

    public function testRejectsNullIfInvalidParent() {
        $this->context->getObject()->parent = new SingleText();
        $this->validator->validate(null, new AssertSlotSupportedByParent());

        $this->buildViolation(AssertSlotSupportedByParent::PARENT_DOES_NOT_SUPPORT_CHILDREN)
            ->assertRaised()
        ;
    }

    public function testRejectsSlotIfInvalidParent() {
        $this->context->getObject()->parent = new SingleText();
        $this->validator->validate('1', new AssertSlotSupportedByParent());

        $this->buildViolation(AssertSlotSupportedByParent::PARENT_DOES_NOT_SUPPORT_CHILDREN)
            ->assertRaised()
        ;
    }

    public function testRejectsNullIfParentIsNotNull() {
        $this->validator->validate(null, new AssertSlotSupportedByParent());

        $this->buildViolation(AssertSlotSupportedByParent::MESSAGE)
            ->setParameter('{{ supportedSlotNames }}', join(',', $this->parent->getSupportedSlotNames()))
            ->setParameter('{{ value }}', 'null')
            ->assertRaised()
        ;
    }

    public function testUsesSelfDefinedMessage() {
        $slotName = 'invalidSlot';
        $this->validator->validate($slotName, new AssertSlotSupportedByParent(message: self::MESSAGE));

        $this->buildViolation(self::MESSAGE)
            ->setParameter('{{ supportedSlotNames }}', join(',', $this->parent->getSupportedSlotNames()))
            ->setParameter('{{ value }}', $slotName)
            ->assertRaised()
        ;
    }

    public function testExpectsMatchingAnnotation() {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate(null, new Email());
    }

    public function testExpectsContentNodeAsObject() {
        $this->context->setNode(
            $this->context->getValue(),
            new Camp(),
            $this->context->getMetadata(),
            $this->context->getPropertyPath()
        );

        $this->expectException(UnexpectedValueException::class);
        $this->validator->validate(null, new AssertSlotSupportedByParent());
    }

    protected function createContext(): ExecutionContext {
        $this->parent = new ColumnLayout();
        $this->parent->setData([
            [
                'slot' => '1',
                'width' => 3,
            ],
            [
                'slot' => '2',
                'width' => 9,
            ],
        ]);

        $object = new ColumnLayout();
        $this->parent->addChild($object);

        $executionContext = parent::createContext();
        $executionContext->setNode(
            $executionContext->getValue(),
            $object,
            $executionContext->getMetadata(),
            $executionContext->getPropertyPath()
        );

        return $executionContext;
    }

    protected function createValidator(): AssertSlotSupportedByParentValidator {
        return new AssertSlotSupportedByParentValidator();
    }
}
