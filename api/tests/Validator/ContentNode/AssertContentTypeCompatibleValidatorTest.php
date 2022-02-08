<?php

namespace App\Tests\Validator\ContentNode;

use App\Entity\Camp;
use App\Entity\ContentNode\ColumnLayout;
use App\Entity\ContentType;
use App\Validator\ContentNode\AssertContentTypeCompatible;
use App\Validator\ContentNode\AssertContentTypeCompatibleValidator;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @internal
 */
class AssertContentTypeCompatibleValidatorTest extends ConstraintValidatorTestCase {
    public function testExpectsMatchingAnnotation() {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate(null, new Email());
    }

    public function testExpectsContentNodeObject() {
        // given
        $contentType = $this->createMock(ContentType::class);
        $this->setObject(new \stdClass());

        // then
        $this->expectException(UnexpectedValueException::class);

        // when
        $this->validator->validate($contentType, new AssertContentTypeCompatible());
    }

    public function testExpectsContentTypeValue() {
        // given
        $contentType = $this->createMock(Camp::class);
        $contentNode = new ColumnLayout();
        $this->setObject($contentNode);

        // then
        $this->expectException(UnexpectedValueException::class);

        // when
        $this->validator->validate($contentType, new AssertContentTypeCompatible());
    }

    public function testValid() {
        // given
        $contentType = $this->createMock(ContentType::class);
        $contentType->entityClass = ColumnLayout::class;

        $contentNode = new ColumnLayout();

        $this->setObject($contentNode);

        // when
        $this->validator->validate($contentType, new AssertContentTypeCompatible());

        // then
        $this->assertNoViolation();
    }

    public function testInvalid() {
        // given
        $contentType = $this->createMock(ContentType::class);
        $contentType->name = 'DummyContentType';
        $contentType->entityClass = 'App\Entity\DummyEntity';

        $contentNode = new ColumnLayout();

        $this->setObject($contentNode);

        // when
        $this->validator->validate($contentType, new AssertContentTypeCompatible());

        // then
        $this->buildViolation('Selected contentType {{ contentTypeName }} is incompatible with entity of type {{ givenEntityClass }} (it can only be used with entities of type {{ expectedEntityClass }}).')
            ->setParameter('{{ contentTypeName }}', 'DummyContentType')
            ->setParameter('{{ givenEntityClass }}', ColumnLayout::class)
            ->setParameter('{{ expectedEntityClass }}', 'App\Entity\DummyEntity')
            ->assertRaised()
        ;
    }

    protected function createValidator() {
        return new AssertContentTypeCompatibleValidator();
    }
}
