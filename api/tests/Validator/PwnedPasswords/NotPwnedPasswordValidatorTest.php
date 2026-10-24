<?php

namespace App\Tests\Validator\PwnedPasswords;

use App\Validator\PwnedPasswords\NotPwnedPassword;
use App\Validator\PwnedPasswords\NotPwnedPasswordValidator;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * @internal
 */
class NotPwnedPasswordValidatorTest extends ConstraintValidatorTestCase {
    public function testPasswordListIsCappedAndContainsUniquePlaintextValues() {
        $passwords = $this->loadPasswords();

        self::assertCount(3000, $passwords);
        self::assertCount(\count($passwords), array_unique(array_map(static fn (string $password): string => mb_strtolower($password, 'UTF-8'), $passwords)));
        self::assertNotContains('', $passwords);
        self::assertNull(array_find($passwords, static fn (string $password): bool => \mb_strlen($password, 'UTF-8') < 12));
    }

    public function testPwnedPasswordIsRejected() {
        $password = $this->loadPasswords()[0];

        $this->validator->validate($password, new NotPwnedPassword());

        $this->buildViolation((new NotPwnedPassword())->message)
            ->setCode(NotPwnedPassword::PWNED_ERROR)
            ->assertRaised()
        ;
    }

    public function testPlaintextPasswordNotInListIsAccepted() {
        $this->validator->validate('definitely-not-in-password-list-2026', new NotPwnedPassword());

        $this->assertNoViolation();
    }

    public function testPasswordMatchingIsCaseInsensitive() {
        $password = array_find($this->loadPasswords(), static fn (string $password): bool => 1 === preg_match('/[A-Za-z]/', $password));
        self::assertNotNull($password);
        $variant = preg_replace_callback(
            '/[A-Za-z]/',
            static fn (array $match): string => ctype_lower($match[0]) ? strtoupper($match[0]) : strtolower($match[0]),
            $password,
            1
        );
        self::assertNotFalse($variant);
        self::assertNotSame($password, $variant);

        $this->validator->validate($variant, new NotPwnedPassword());

        $this->buildViolation((new NotPwnedPassword())->message)->setCode(NotPwnedPassword::PWNED_ERROR)->assertRaised();
    }

    public function testPasswordWithPrefixOrSuffixIsAccepted() {
        $password = $this->loadPasswords()[0];

        $this->validator->validate('prefix'.$password, new NotPwnedPassword());
        $this->assertNoViolation();

        $this->validator->validate($password.'suffix', new NotPwnedPassword());
        $this->assertNoViolation();
    }

    protected function createValidator(): ConstraintValidatorInterface {
        return new NotPwnedPasswordValidator();
    }

    private function loadPasswords(): array {
        return file(__DIR__.'/../../../src/Validator/PwnedPasswords/password-list.txt', FILE_IGNORE_NEW_LINES);
    }
}
