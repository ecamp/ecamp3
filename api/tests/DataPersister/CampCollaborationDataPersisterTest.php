<?php

namespace App\Tests\DataPersister;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\DataPersister\CampCollaborationDataPersister;
use App\DataPersister\Util\DataPersisterObservable;
use App\Entity\Camp;
use App\Entity\CampCollaboration;
use App\Entity\MaterialList;
use App\Entity\Profile;
use App\Entity\User;
use App\Repository\ProfileRepository;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Constraint\Callback;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Security\Core\Security;

/**
 * @internal
 */
class CampCollaborationDataPersisterTest extends TestCase {
    public const INITIAL_USER = null;
    public const INITIAL_INVITE_EMAIL = null;
    public const INITIAL_INVITE_KEY = null;

    private CampCollaboration $campCollaboration;
    private User $user;
    private Profile $profile;
    private Camp $camp;

    private MockObject|ProfileRepository $profileRepository;
    private MockObject|EntityManagerInterface $em;

    private MockObject|Security $security;
    private MockObject|PasswordHasherFactoryInterface $pwHashFactory;
    private MockObject|MailService $mailService;

    private CampCollaborationDataPersister $dataPersister;

    /**
     * @throws \ReflectionException
     */
    protected function setUp(): void {
        $this->camp = new Camp();
        $this->camp->title = 'title';

        $this->campCollaboration = new CampCollaboration();
        $this->campCollaboration->user = self::INITIAL_USER;
        $this->campCollaboration->inviteEmail = self::INITIAL_INVITE_EMAIL;
        $this->campCollaboration->inviteKey = self::INITIAL_INVITE_KEY;
        $this->campCollaboration->camp = $this->camp;

        $this->profile = new Profile();
        $this->profile->email = 'e@mail.com';
        $this->user = new User();
        $this->profile->user = $this->user;
        $this->user->profile = $this->profile;

        $dataPersisterObservable = $this->createMock(DataPersisterObservable::class);
        $this->security = $this->createMock(Security::class);
        $this->security->expects(self::any())->method('getUser')->willReturn($this->user);
        $this->pwHashFactory = $this->createMock(PasswordHasherFactory::class);
        $this->profileRepository = $this->createMock(ProfileRepository::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->mailService = $this->createMock(MailService::class);
        $validator = $this->createMock(ValidatorInterface::class);

        $this->dataPersister = new CampCollaborationDataPersister(
            $dataPersisterObservable,
            $this->security,
            $this->pwHashFactory,
            $this->profileRepository,
            $this->em,
            $this->mailService,
            $validator
        );
    }

    public function testAfterCreateSendsEmailIfInviteEmailSet() {
        $this->campCollaboration->inviteEmail = 'e@mail.com';

        $this->mailService->expects(self::once())->method('sendInviteToCampMail');

        $result = $this->dataPersister->beforeCreate($this->campCollaboration);
        $this->dataPersister->afterCreate($result);
    }

    public function testAfterCreateCreatesMaterialList() {
        $this->campCollaboration->inviteEmail = 'e@mail.com';
        $this->security->expects(self::once())->method('getUser')->willReturn($this->user);

        $this->em
            ->expects(self::once())
            ->method('persist')
            ->with(self::materialListWith($this->campCollaboration, $this->camp))
        ;

        $result = $this->dataPersister->beforeCreate($this->campCollaboration);
        $this->dataPersister->afterCreate($result);
    }

    public function testAfterCreateSendsEmailIfUserSet() {
        $this->campCollaboration->inviteEmail = 'e@mail.com';
        $this->profileRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['email' => $this->campCollaboration->inviteEmail])
            ->willReturn($this->profile)
        ;
        $this->security->expects(self::once())->method('getUser')->willReturn($this->user);

        $this->mailService->expects(self::once())->method('sendInviteToCampMail');

        $result = $this->dataPersister->beforeCreate($this->campCollaboration);
        $this->dataPersister->afterCreate($result);
    }

    public function testAfterCreateDoesNotSendEmailIfNoInviteEmailSet() {
        $this->security->expects(self::once())->method('getUser')->willReturn($this->user);

        $this->mailService->expects(self::never())->method('sendInviteToCampMail');

        $result = $this->dataPersister->beforeCreate($this->campCollaboration);
        $this->dataPersister->afterCreate($result);
    }

    /**
     * @dataProvider notInvitedStatuses
     */
    public function testAfterCreateDoesNotSendEmailIfStatusNotInvited($status) {
        $this->campCollaboration->inviteEmail = 'e@mail.com';
        $this->campCollaboration->status = $status;
        $this->security->expects(self::once())->method('getUser')->willReturn($this->user);

        $this->mailService->expects(self::never())->method('sendInviteToCampMail');

        $result = $this->dataPersister->beforeCreate($this->campCollaboration);
        $this->dataPersister->afterCreate($result);
    }

    public function testDoesNothingOnStatusChangeWhenNoInviteEmail() {
        $this->mailService->expects(self::never())->method('sendInviteToCampMail');

        $result = $this->dataPersister->onBeforeStatusChange($this->campCollaboration);
        $this->dataPersister->onAfterStatusChange($this->campCollaboration);

        self::assertThat($result->user, self::equalTo(self::INITIAL_USER));
        self::assertThat($result->inviteEmail, self::equalTo(self::INITIAL_INVITE_EMAIL));
        self::assertThat($result->inviteKey, self::equalTo(self::INITIAL_INVITE_KEY));
    }

    /**
     * @dataProvider notInvitedStatuses
     */
    public function testOnStatusChangeDoesNothingIfStatusIsNotInvited($status) {
        $this->campCollaboration->inviteEmail = 'e@mail.com';
        $this->campCollaboration->status = $status;

        $this->mailService->expects(self::never())->method('sendInviteToCampMail');

        $result = $this->dataPersister->onBeforeStatusChange($this->campCollaboration);
        $this->dataPersister->onAfterStatusChange($this->campCollaboration);

        self::assertThat($result->user, self::equalTo(self::INITIAL_USER));
        self::assertThat($result->inviteEmail, self::equalTo($this->campCollaboration->inviteEmail));
        self::assertThat($result->inviteKey, self::equalTo(self::INITIAL_INVITE_KEY));
    }

    public function testSendsInviteEmailIfStatusChangesToInvitedAndInviteEmailPresent() {
        $this->campCollaboration->inviteEmail = 'e@mail.com';
        $this->mailService->expects(self::once())->method('sendInviteToCampMail');

        $result = $this->dataPersister->onBeforeStatusChange($this->campCollaboration);
        $this->dataPersister->onAfterStatusChange($this->campCollaboration);

        self::assertThat($result->inviteKey, self::logicalNot(self::isNull()));
    }

    public function testSendsInviteEmailIfStatusChangesToInvitedAndUserPresent() {
        $this->campCollaboration->user = $this->user;
        $this->mailService->expects(self::once())->method('sendInviteToCampMail');

        $result = $this->dataPersister->onBeforeStatusChange($this->campCollaboration);
        $this->dataPersister->onAfterStatusChange($this->campCollaboration);

        self::assertThat($result->inviteKey, self::logicalNot(self::isNull()));
    }

    /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */
    public static function notInvitedStatuses(): array {
        return [
            CampCollaboration::STATUS_INACTIVE => [CampCollaboration::STATUS_INACTIVE],
            CampCollaboration::STATUS_ESTABLISHED => [CampCollaboration::STATUS_ESTABLISHED],
        ];
    }

    private static function materialListWith(CampCollaboration $campCollaboration, Camp $camp): Callback {
        return self::callback(function ($objectToPersist) use ($campCollaboration, $camp) {
            if (!$objectToPersist instanceof MaterialList) {
                return false;
            }

            return $campCollaboration === $objectToPersist->campCollaboration
                && $camp === $objectToPersist->getCamp()
                && null === $objectToPersist->name;
        });
    }
}
