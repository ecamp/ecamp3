<?php

namespace App\Tests\State;

use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Camp;
use App\Entity\CampCollaboration;
use App\Entity\MaterialList;
use App\Entity\Profile;
use App\Entity\User;
use App\State\CampCreateProcessor;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Constraint\Callback;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Security;

/**
 * @internal
 */
class CampCreateProcessorTest extends TestCase {
    private CampCreateProcessor $processor;
    private MockObject|Security $security;
    private MockObject|EntityManagerInterface $em;
    private Camp $camp;

    protected function setUp(): void {
        $this->camp = new Camp();

        $this->security = $this->createMock(Security::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $decoratedProcessor = $this->createMock(ProcessorInterface::class);
        $this->processor = new CampCreateProcessor($decoratedProcessor, $this->security, $this->em);
    }

    public function testSetsCreatorAndOwnerOnCreate() {
        // given
        $user = new User();
        $this->security->method('getUser')->willReturn($user);

        // when
        /** @var Camp $data */
        $data = $this->processor->onBefore($this->camp, new Post());

        // then
        $this->assertEquals($user, $data->creator);
        $this->assertEquals($user, $data->owner);
    }

    public function testCreatesCampCollaborationAndMaterialListAfterCreate() {
        // given
        $profile = new Profile();
        $profile->nickname = 'test';
        $user = new User();
        $user->profile = $profile;
        $this->security->method('getUser')->willReturn($user);

        $this->em
            ->expects($this->exactly(2))
            ->method('persist')
            ->withConsecutive(
                [
                    self::campCollaborationWith(
                        $user,
                        $this->camp,
                        CampCollaboration::STATUS_ESTABLISHED,
                        CampCollaboration::ROLE_MANAGER
                    ),
                ],
                [
                    self::materialListWith(
                        $this->camp
                    ),
                ]
            )
        ;

        // when
        $this->processor->onAfter($this->camp, new Post());
    }

    private static function campCollaborationWith(User $user, Camp $camp, string $status, string $role): Callback {
        return self::callback(function ($object) use ($user, $camp, $status, $role) {
            if (!$object instanceof CampCollaboration) {
                return false;
            }
            $campCollaboration = $object;

            return $user === $campCollaboration->user
                && $camp === $campCollaboration->camp
                && $status === $campCollaboration->status
                && $role === $campCollaboration->role
                && in_array($campCollaboration, $camp->collaborations->toArray());
        });
    }

    private static function materialListWith(Camp $camp): Callback {
        return self::callback(function ($objectToPersist) use ($camp) {
            if (!$objectToPersist instanceof MaterialList) {
                return false;
            }

            return null !== $objectToPersist->campCollaboration
                && $camp === $objectToPersist->getCamp()
                && null === $objectToPersist->name;
        });
    }
}
