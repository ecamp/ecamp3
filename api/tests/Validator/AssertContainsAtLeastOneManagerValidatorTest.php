<?php

namespace App\Tests\Validator;

use App\Entity\CampCollaboration;
use App\Entity\User;
use App\Validator\AssertContainsAtLeastOneManager;
use App\Validator\AssertContainsAtLeastOneManagerValidator;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @internal
 */
class AssertContainsAtLeastOneManagerValidatorTest extends ConstraintValidatorTestCase {
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

    public function testExpectsCollectionValue() {
        $this->expectException(UnexpectedValueException::class);
        $this->validator->validate(new \stdClass(), new AssertContainsAtLeastOneManager());
    }

    public function testExpectsItemsOfTypeCampCollaboration() {
        $this->expectException(UnexpectedValueException::class);
        $this->validator->validate(new ArrayCollection(['test']), new AssertContainsAtLeastOneManager());
    }

    public function testValidationSucceedsIfValueIsNull() {
        $this->validator->validate(null, new AssertContainsAtLeastOneManager());

        $this->assertNoViolation();
    }

    public function testValidationFailsIfCampHasNoCampCollaborations() {
        $this->validator->validate(new ArrayCollection(), new AssertContainsAtLeastOneManager());

        $this->buildViolation('must have at least one manager.')
            ->assertRaised()
        ;
    }

    public function testValidationFailsIfCollectionHasOnlyMembersAndGuests() {
        $collection = new ArrayCollection();
        $collection->add(
            self::createCampCollaboration(self::GUEST, self::ESTABLISHED)
        );
        $collection->add(
            self::createCampCollaboration(self::MEMBER, self::ESTABLISHED)
        );
        $this->validator->validate($collection, new AssertContainsAtLeastOneManager());

        $this->buildViolation('must have at least one manager.')
            ->assertRaised()
        ;
    }

    public function testValidationFailsIfManagerIsNotEstablished() {
        $collection = new ArrayCollection();
        $collection->add(
            self::createCampCollaboration(self::MANAGER, self::INACTIVE)
        );
        $collection->add(
            self::createCampCollaboration(self::MANAGER, self::INVITED)
        );
        $this->validator->validate($collection, new AssertContainsAtLeastOneManager());

        $this->buildViolation('must have at least one manager.')
            ->assertRaised()
        ;
    }

    public function testValidationSucceedsIfCampHasOneManager() {
        $collection = new ArrayCollection();
        $collection->add(
            self::createCampCollaboration(self::MANAGER, self::ESTABLISHED)
        );
        $this->validator->validate($collection, new AssertContainsAtLeastOneManager());

        $this->assertNoViolation();
    }

    protected function createValidator(): ConstraintValidatorInterface {
        return new AssertContainsAtLeastOneManagerValidator();
    }

    private static function createCampCollaboration($role, $status): CampCollaboration {
        $campCollaboration = new CampCollaboration();
        $campCollaboration->user = new User();
        $campCollaboration->role = $role;
        $campCollaboration->status = $status;

        return $campCollaboration;
    }
}
