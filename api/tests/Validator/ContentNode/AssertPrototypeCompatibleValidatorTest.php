<?php

namespace App\Tests\Validator\ContentNode;

use App\Entity\ContentNode\ColumnLayout;
use App\Entity\ContentNode\SingleText;
use App\Entity\ContentType;
use App\Validator\ContentNode\AssertPrototypeCompatible;
use App\Validator\ContentNode\AssertPrototypeCompatibleValidator;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @internal
 */
class AssertPrototypeCompatibleValidatorTest extends ConstraintValidatorTestCase {
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
        $this->validator->validate($contentType, new AssertPrototypeCompatible());
    }

    public function testExpectsContentNodeValue() {
        $this->expectException(UnexpectedValueException::class);
        $this->validator->validate(new \stdClass(), new AssertPrototypeCompatible());
    }

    public function testEmptyPrototypeIsValid() {
        // given
        $contentType2 = new ContentType();
        $contentType2->name = 'ColumnLayout';

        $contentNode = new ColumnLayout();
        $contentNode->contentType = $contentType2;

        $this->setObject($contentNode);

        // when
        $this->validator->validate(null, new AssertPrototypeCompatible());

        // then
        $this->assertNoViolation();
    }

    public function testValid() {
        // given
        $contentType1 = new ContentType();
        $contentType1->name = 'ColumnLayout';

        $prototype = new ColumnLayout();
        $prototype->contentType = $contentType1;

        $contentType2 = new ContentType();
        $contentType2->name = 'ColumnLayout';

        $contentNode = new ColumnLayout();
        $contentNode->contentType = $contentType2;

        $this->setObject($contentNode);

        // when
        $this->validator->validate($prototype, new AssertPrototypeCompatible());

        // then
        $this->assertNoViolation();
    }

    public function testInvalidWhenContentTypeDoesntMatch() {
        // given
        $contentType1 = new ContentType();
        $contentType1->name = 'Notes';

        $prototype = new ColumnLayout();
        $prototype->contentType = $contentType1;

        $contentType2 = new ContentType();
        $contentType2->name = 'ColumnLayout';

        $contentNode = new ColumnLayout();
        $contentNode->contentType = $contentType2;

        $this->setObject($contentNode);

        // when
        $this->validator->validate($prototype, new AssertPrototypeCompatible());

        // then
        $this->buildViolation('This value must have the content type {{ expectedContentType }}, but was {{ actualContentType }}.')
            ->setParameter('{{ expectedContentType }}', 'ColumnLayout')
            ->setParameter('{{ actualContentType }}', 'Notes')
            ->assertRaised()
        ;
    }

    public function testInvalidWhenPrototypeClassDoesntMatch() {
        // given
        $contentType1 = new ContentType();
        $contentType1->name = 'ColumnLayout';

        $prototype = new SingleText();
        $prototype->contentType = $contentType1;

        $contentType2 = new ContentType();
        $contentType2->name = 'ColumnLayout';

        $contentNode = new ColumnLayout();
        $contentNode->contentType = $contentType2;

        $this->setObject($contentNode);

        // when
        $this->validator->validate($prototype, new AssertPrototypeCompatible());

        // then
        $this->buildViolation('This value must be an instance of {{ expectedClass }} or a subclass, but was {{ actualClass }}.')
            ->setParameter('{{ expectedClass }}', ColumnLayout::class)
            ->setParameter('{{ actualClass }}', SingleText::class)
            ->assertRaised()
        ;
    }

    protected function createValidator() {
        return new AssertPrototypeCompatibleValidator();
    }
}
