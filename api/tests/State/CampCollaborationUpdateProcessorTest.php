<?php

namespace App\Tests\State;

use ApiPlatform\State\ProcessorInterface;
use App\Entity\Camp;
use App\Entity\CampCollaboration;
use App\Entity\Profile;
use App\Entity\User;
use App\Service\MailService;
use App\State\CampCollaborationUpdateProcessor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;

/**
 * @internal
 */
class CampCollaborationUpdateProcessorTest extends TestCase {
    use CampCollaborationTestTrait;

    public const INITIAL_USER = null;
    public const INITIAL_INVITE_EMAIL = null;
    public const INITIAL_INVITE_KEY = null;

    private CampCollaboration $campCollaboration;
    private User $user;

    private MockObject|MailService $mailService;

    private CampCollaborationUpdateProcessor $processor;

    /**
     * @throws \ReflectionException
     */
    protected function setUp(): void {
        $camp = new Camp();
        $camp->title = 'title';

        $this->campCollaboration = new CampCollaboration();
        $this->campCollaboration->user = self::INITIAL_USER;
        $this->campCollaboration->inviteEmail = self::INITIAL_INVITE_EMAIL;
        $this->campCollaboration->inviteKey = self::INITIAL_INVITE_KEY;
        $this->campCollaboration->camp = $camp;

        $profile = new Profile();
        $profile->email = 'e@mail.com';
        $this->user = new User();
        $profile->user = $this->user;
        $this->user->profile = $profile;

        $decoratedProcessor = $this->createMock(ProcessorInterface::class);
        $security = $this->createMock(Security::class);
        $security->expects(self::any())->method('getUser')->willReturn($this->user);
        $pwHashFactory = $this->createMock(PasswordHasherFactory::class);
        $this->mailService = $this->createMock(MailService::class);

        $this->processor = new CampCollaborationUpdateProcessor(
            $decoratedProcessor,
            $security,
            $pwHashFactory,
            $this->mailService,
        );
    }

    public function testDoesNothingOnStatusChangeWhenNoInviteEmail() {
        $this->mailService->expects(self::never())->method('sendInviteToCampMail');

        $result = $this->processor->onBeforeStatusChange($this->campCollaboration);
        $this->processor->onAfterStatusChange($this->campCollaboration);

        self::assertThat($result->user, self::equalTo(self::INITIAL_USER));
        self::assertThat($result->inviteEmail, self::equalTo(self::INITIAL_INVITE_EMAIL));
        self::assertThat($result->inviteKey, self::equalTo(self::INITIAL_INVITE_KEY));
    }

    /**
     * @dataProvider notInvitedStatuses
     *
     * @param string $status
     */
    public function testOnStatusChangeDoesNothingIfStatusIsNotInvited($status) {
        $this->campCollaboration->inviteEmail = 'e@mail.com';
        $this->campCollaboration->status = $status;

        $this->mailService->expects(self::never())->method('sendInviteToCampMail');

        $result = $this->processor->onBeforeStatusChange($this->campCollaboration);
        $this->processor->onAfterStatusChange($this->campCollaboration);

        self::assertThat($result->user, self::equalTo(self::INITIAL_USER));
        self::assertThat($result->inviteEmail, self::equalTo($this->campCollaboration->inviteEmail));
        self::assertThat($result->inviteKey, self::equalTo(self::INITIAL_INVITE_KEY));
    }

    public function testSendsInviteEmailIfStatusChangesToInvitedAndInviteEmailPresent() {
        $this->campCollaboration->inviteEmail = 'e@mail.com';
        $this->mailService->expects(self::once())->method('sendInviteToCampMail');

        $result = $this->processor->onBeforeStatusChange($this->campCollaboration);
        $this->processor->onAfterStatusChange($this->campCollaboration);

        self::assertThat($result->inviteKey, self::logicalNot(self::isNull()));
    }

    public function testSendsInviteEmailIfStatusChangesToInvitedAndUserPresent() {
        $this->campCollaboration->user = $this->user;
        $this->mailService->expects(self::once())->method('sendInviteToCampMail');

        $result = $this->processor->onBeforeStatusChange($this->campCollaboration);
        $this->processor->onAfterStatusChange($this->campCollaboration);

        self::assertThat($result->inviteKey, self::logicalNot(self::isNull()));
    }
}
