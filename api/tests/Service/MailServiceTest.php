<?php

namespace App\Tests\Service;

use App\DTO\ResetPassword;
use App\Entity\Camp;
use App\Entity\Profile;
use App\Entity\User;
use App\Service\MailService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @internal
 */
class MailServiceTest extends KernelTestCase {
    public const INVITE_MAIL = 'invite@mail.com';
    public const INVITE_KEY = 'key';

    private Camp $camp;
    private User $user;

    private MailService $mailer;

    protected function setUp(): void {
        static::bootKernel();

        /** @var MailService $mailer */
        $mailer = self::getContainer()->get(MailService::class);
        $this->mailer = $mailer;

        $this->user = new User();
        $profile = new Profile();
        $profile->nickname = 'coolScoutName';
        $this->user->profile = $profile;

        $this->camp = new Camp();
        $this->camp->name = 'some camp';
    }

    public function testSendInviteToCampMailDeChScout() {
        $this->user->profile->language = 'de-CH-scout';
        $this->mailer->sendInviteToCampMail($this->user, $this->camp, self::INVITE_KEY, self::INVITE_MAIL);

        self::assertEmailCount(1);
        $mailerMessage = self::getMailerMessage(0);
        self::assertEmailAddressContains($mailerMessage, 'To', self::INVITE_MAIL);

        self::assertEmailHtmlBodyContains($mailerMessage, $this->camp->name);
        self::assertEmailHtmlBodyContains($mailerMessage, $this->user->getDisplayName());
        self::assertEmailHtmlBodyContains($mailerMessage, self::INVITE_KEY);

        self::assertEmailTextBodyContains($mailerMessage, $this->camp->name);
        self::assertEmailTextBodyContains($mailerMessage, $this->user->getDisplayName());
        self::assertEmailTextBodyContains($mailerMessage, self::INVITE_KEY);
    }

    public function testSendUserActivationMailDeChScout() {
        $this->user->profile->language = 'de-CH-scout';
        $this->user->profile->email = self::INVITE_MAIL;
        $this->mailer->sendUserActivationMail($this->user, self::INVITE_KEY);

        self::assertEmailCount(1);
        $mailerMessage = self::getMailerMessage(0);
        self::assertEmailAddressContains($mailerMessage, 'To', self::INVITE_MAIL);

        self::assertEmailHtmlBodyContains($mailerMessage, 'Willkommen');
        self::assertEmailHtmlBodyContains($mailerMessage, self::INVITE_KEY);
        self::assertEmailTextBodyContains($mailerMessage, 'Willkommen');
        self::assertEmailTextBodyContains($mailerMessage, self::INVITE_KEY);
    }

    public function testSendUserActivationMailFr() {
        // Test fallback to english
        $this->user->profile->language = 'fr';
        $this->user->profile->email = self::INVITE_MAIL;
        $this->mailer->sendUserActivationMail($this->user, self::INVITE_KEY);

        self::assertEmailCount(1);
        $mailerMessage = self::getMailerMessage(0);
        self::assertEmailAddressContains($mailerMessage, 'To', self::INVITE_MAIL);

        self::assertEmailHtmlBodyContains($mailerMessage, 'Welcome');
        self::assertEmailHtmlBodyContains($mailerMessage, self::INVITE_KEY);
        self::assertEmailTextBodyContains($mailerMessage, 'Welcome');
        self::assertEmailTextBodyContains($mailerMessage, self::INVITE_KEY);
    }

    public function testSendResetPasswordMailDeChScout() {
        $this->user->profile->language = 'de-CH-scout';
        $this->user->profile->email = self::INVITE_MAIL;

        $resetPassword = new ResetPassword();
        $resetPassword->emailBase64 = 'mail';
        $resetPassword->resetKey = 'resetKey';

        $this->mailer->sendPasswordResetLink($this->user, $resetPassword);

        self::assertEmailCount(1);
        $mailerMessage = self::getMailerMessage(0);
        self::assertEmailAddressContains($mailerMessage, 'To', self::INVITE_MAIL);

        self::assertEmailHtmlBodyContains($mailerMessage, 'Passwort zurücksetzen');
        self::assertEmailHtmlBodyContains($mailerMessage, 'reset-password/mail/resetKey');
        self::assertEmailTextBodyContains($mailerMessage, 'Passwort zurücksetzen');
        self::assertEmailTextBodyContains($mailerMessage, 'reset-password/mail/resetKey');
    }
}
