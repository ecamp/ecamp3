<?php

namespace App\Tests\Validator;

use App\Entity\Camp;
use App\Entity\CampCollaboration;
use App\Entity\User;
use App\Validator\AssertHasAtLeastOneManager;
use App\Validator\AssertHasAtLeastOneManagerValidator;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @internal
 */
class AssertHasAtLeastOneManagerValidatorTest extends ConstraintValidatorTestCase {
    public const GUEST = CampCollaboration::ROLE_GUEST;
    public const MEMBER = CampCollaboration::ROLE_MEMBER;
    public const MANAGER = CampCollaboration::ROLE_MANAGER;

    public const INVITED = CampCollaboration::STATUS_INVITED;
    public const ESTABLISHED = CampCollaboration::STATUS_ESTABLISHED;
    public const INACTIVE = CampCollaboration::STATUS_INACTIVE;

    public function testExpectsMatchingAnnotation() {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate(null, new Email());
    }

    public function testExpectsCampValue() {
        $this->expectException(UnexpectedValueException::class);
        $this->validator->validate(new \stdClass(), new AssertHasAtLeastOneManager());
    }

    public function testValidationSucceedsIfValueIsNull() {
        $this->validator->validate(null, new AssertHasAtLeastOneManager());

        $this->assertNoViolation();
    }

    public function testValidationFailsIfCampHasNoCampCollaborations() {
        $this->validator->validate(new Camp(), new AssertHasAtLeastOneManager());

        $this->buildViolation('Camp must have at least one manager.')
            ->assertRaised()
        ;
    }

    public function testValidationFailsIfCampHasOnlyMembersAndGuests() {
        $camp = new Camp();
        $camp->addCampCollaboration(
            self::createCampCollaboration(self::GUEST, self::ESTABLISHED)
        );
        $camp->addCampCollaboration(
            self::createCampCollaboration(self::MEMBER, self::ESTABLISHED)
        );
        $this->validator->validate($camp, new AssertHasAtLeastOneManager());

        $this->buildViolation('Camp must have at least one manager.')
            ->assertRaised()
        ;
    }

    public function testValidationFailsIfManagerIsNotEstablished() {
        $camp = new Camp();
        $camp->addCampCollaboration(
            self::createCampCollaboration(self::MANAGER, self::INACTIVE)
        );
        $camp->addCampCollaboration(
            self::createCampCollaboration(self::MANAGER, self::INVITED)
        );
        $this->validator->validate($camp, new AssertHasAtLeastOneManager());

        $this->buildViolation('Camp must have at least one manager.')
            ->assertRaised()
        ;
    }

    public function testValidationSucceedsIfCampHasOneManager() {
        $camp = new Camp();
        $camp->addCampCollaboration(
            self::createCampCollaboration(self::MANAGER, self::ESTABLISHED)
        );
        $this->validator->validate($camp, new AssertHasAtLeastOneManager());

        $this->assertNoViolation();
    }

    protected function createValidator() {
        return new AssertHasAtLeastOneManagerValidator();
    }

    private static function createCampCollaboration($role, $status): CampCollaboration {
        $campCollaboration = new CampCollaboration();
        $campCollaboration->user = new User();
        $campCollaboration->role = $role;
        $campCollaboration->status = $status;

        return $campCollaboration;
    }
}
