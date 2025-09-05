<?php

namespace App\Tests\Doctrine\Orm;

use App\Doctrine\Orm\GetOriginalEntityData;
use App\Entity\Profile;
use App\Entity\User;
use App\Tests\Api\ECampApiTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;

use function PHPUnit\Framework\assertThat;
use function PHPUnit\Framework\equalTo;
use function PHPUnit\Framework\isFalse;
use function PHPUnit\Framework\logicalNot;

/**
 * @internal
 */
class GetOriginalDataIntegrationTest extends ECampApiTestCase {
    private EntityManagerInterface $em;
    private GetOriginalEntityData $getOriginalEntityData;

    public function setUp(): void {
        parent::setUp();
        $container = static::getContainer();
        $this->em = $container->get(EntityManagerInterface::class);
        $this->getOriginalEntityData = $container->get(GetOriginalEntityData::class);
    }

    public function testGetPreviousStateOfEntity() {
        /** @var User $user */
        $user = static::getFixture('user1manager');
        $this->em->persist($user);
        $previousState = $user->state;
        assertThat($previousState, logicalNot(equalTo(User::STATE_NONREGISTERED)));

        $user->state = User::STATE_NONREGISTERED;
        $this->recomputeChangeSet($user);
        $original = $this->getOriginalEntityData->getOriginal($user);

        assertThat($original->state, equalTo($previousState));
    }

    public function testGetPreviousStateOfUnchangedEntity() {
        /** @var User $user */
        $user = static::getFixture('user1manager');
        $this->em->persist($user);
        $this->computeChangeSet($user);

        $this->recomputeChangeSet($user);
        $original = $this->getOriginalEntityData->getOriginal($user);

        assertThat(isset($original->state), isFalse());
    }

    public function testGetPreviousStateOfNewEntity() {
        $user = new User();
        $user->profile = new Profile();
        $user->profile->email = Uuid::uuid4().'@example.com';
        $previousState = $user->state;
        $this->em->persist($user);
        $this->computeChangeSet($user);
        assertThat($previousState, logicalNot(equalTo(User::STATE_REGISTERED)));

        $user->state = User::STATE_REGISTERED;
        $this->recomputeChangeSet($user);
        $original = $this->getOriginalEntityData->getOriginal($user);

        assertThat($original->state, equalTo($previousState));
    }

    public function testGetPreviousStateOfUnchangedNewEntity() {
        $user = new User();
        $user->profile = new Profile();
        $user->profile->email = Uuid::uuid4().'@example.com';
        $this->em->persist($user);
        $this->computeChangeSet($user);

        $this->recomputeChangeSet($user);
        $original = $this->getOriginalEntityData->getOriginal($user);

        assertThat(isset($original->state), isFalse());
    }

    private function computeChangeSet(User $user): void {
        $this->em
            ->getUnitOfWork()
            ->computeChangeSet(
                class: $this->em->getClassMetadata(get_class($user)),
                entity: $user
            )
        ;
    }

    private function recomputeChangeSet(User $user): void {
        $this->em
            ->getUnitOfWork()
            ->recomputeSingleEntityChangeSet(
                class: $this->em->getClassMetadata(get_class($user)),
                entity: $user
            )
        ;
    }
}
