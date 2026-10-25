<?php

namespace App\Tests\Validator\Comment;

use App\Entity\Activity;
use App\Entity\Comment;
use App\Validator\Comment\AssertSameAnchorAsParent;
use App\Validator\Comment\AssertSameAnchorAsParentValidator;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @internal
 */
class AssertSameAnchorAsParentValidatorTest extends ConstraintValidatorTestCase {
    public function testExpectsMatchingAnnotation() {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate(null, new Email());
    }

    public function testExpectsCommentValue() {
        $this->setObject(new Comment());

        $this->expectException(UnexpectedValueException::class);

        $this->validator->validate(new \stdClass(), new AssertSameAnchorAsParent());
    }

    public function testExpectsCommentObject() {
        $this->setObject(new \stdClass());

        $this->expectException(UnexpectedValueException::class);

        $this->validator->validate(new Comment(), new AssertSameAnchorAsParent());
    }

    public function testNullIsValid() {
        $this->validator->validate(null, new AssertSameAnchorAsParent());
        $this->assertNoViolation();
    }

    public function testEmptyIsValid() {
        $this->validator->validate('', new AssertSameAnchorAsParent());
        $this->assertNoViolation();
    }

    public function testValidWhenTheReplyRepeatsTheAnchor() {
        $activity = $this->activity('idfromtest');
        $this->setObject($this->comment($activity));

        $this->validator->validate($this->comment($activity), new AssertSameAnchorAsParent());

        $this->assertNoViolation();
    }

    public function testValidWhenTheReplyLeavesOutTheAnchor() {
        $this->setObject($this->comment(null));

        $this->validator->validate($this->comment($this->activity('idfromtest')), new AssertSameAnchorAsParent());

        $this->assertNoViolation();
    }

    public function testValidWhenNeitherHasAnActivity() {
        $this->setObject($this->comment(null));

        $this->validator->validate($this->comment(null), new AssertSameAnchorAsParent());

        $this->assertNoViolation();
    }

    public function testInvalidForAnotherActivity() {
        $this->setObject($this->comment($this->activity('otheridfromtest')));

        $this->validator->validate($this->comment($this->activity('idfromtest')), new AssertSameAnchorAsParent());

        $this->buildViolation('Must be about the same {{ field }} as the comment it replies to.')
            ->setParameter('{{ field }}', 'activity')
            ->assertRaised()
        ;
    }

    public function testInvalidForAnActivityWhenTheParentHasNone() {
        $this->setObject($this->comment($this->activity('idfromtest')));

        $this->validator->validate($this->comment(null), new AssertSameAnchorAsParent());

        $this->buildViolation('Must be about the same {{ field }} as the comment it replies to.')
            ->setParameter('{{ field }}', 'activity')
            ->assertRaised()
        ;
    }

    protected function createValidator(): ConstraintValidatorInterface {
        return new AssertSameAnchorAsParentValidator();
    }

    private function activity(string $id): Activity {
        $activity = $this->createStub(Activity::class);
        $activity->method('getId')->willReturn($id);

        return $activity;
    }

    private function comment(?Activity $activity): Comment {
        $comment = new Comment();
        $comment->activity = $activity;

        return $comment;
    }
}
