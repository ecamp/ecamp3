<?php

namespace App\Tests\State;

use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Validator\ValidatorInterface;
use App\Entity\Camp;
use App\Entity\CampCollaboration;
use App\Entity\MaterialList;
use App\Entity\Profile;
use App\Entity\User;
use App\Repository\ProfileRepository;
use App\Service\MailService;
use App\State\CampCollaborationCreateProcessor;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Constraint\Callback;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

/**
 * @internal
 */
class CampCollaborationCreateProcessorTest extends TestCase {
    use CampCollaborationTestTrait;

    public const INITIAL_USER = null;
    public const INITIAL_INVITE_EMAIL = null;
    public const INITIAL_INVITE_KEY = null;

    private CampCollaboration $campCollaboration;
    private User $user;
    private Profile $profile;
    private Camp $camp;

    private MockObject|ProfileRepository $profileRepository;
    private EntityManagerInterface|MockObject $em;

    private MockObject|Security $security;
    private MockObject|PasswordHasherFactoryInterface $pwHashFactory;
    private MailService|MockObject $mailService;

    private CampCollaborationCreateProcessor $processor;

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

        $decoratedProcessor = $this->createMock(ProcessorInterface::class);
        $this->security = $this->createMock(Security::class);
        $this->security->expects(self::any())->method('getUser')->willReturn($this->user);
        $this->pwHashFactory = $this->createMock(PasswordHasherFactory::class);
        $this->profileRepository = $this->createMock(ProfileRepository::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->mailService = $this->createMock(MailService::class);
        $validator = $this->createMock(ValidatorInterface::class);

        $this->processor = new CampCollaborationCreateProcessor(
            $decoratedProcessor,
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

        $result = $this->processor->onBefore($this->campCollaboration, new Post());
        $this->processor->onAfter($result, new Post());
    }

    public function testAfterCreateCreatesMaterialList() {
        $this->campCollaboration->inviteEmail = 'e@mail.com';
        $this->security->expects(self::once())->method('getUser')->willReturn($this->user);

        $this->em
            ->expects(self::once())
            ->method('persist')
            ->with(self::materialListWith($this->campCollaboration, $this->camp))
        ;

        $result = $this->processor->onBefore($this->campCollaboration, new Post());
        $this->processor->onAfter($result, new Post());
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

        $result = $this->processor->onBefore($this->campCollaboration, new Post());
        $this->processor->onAfter($result, new Post());
    }

    public function testAfterCreateDoesNotSendEmailIfNoInviteEmailSet() {
        $this->mailService->expects(self::never())->method('sendInviteToCampMail');

        $result = $this->processor->onBefore($this->campCollaboration, new Post());
        $this->processor->onAfter($result, new Post());
    }

    /**
     * @dataProvider notInvitedStatuses
     */
    public function testAfterCreateDoesNotSendEmailIfStatusNotInvited(string $status) {
        $this->campCollaboration->inviteEmail = 'e@mail.com';
        $this->campCollaboration->status = $status;

        $this->mailService->expects(self::never())->method('sendInviteToCampMail');

        $result = $this->processor->onBefore($this->campCollaboration, new Post());
        $this->processor->onAfter($result, new Post());
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
