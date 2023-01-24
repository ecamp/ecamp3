<?php

namespace App\Tests\State;

use ApiPlatform\Metadata\Patch;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\State\UserUpdateProcessor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @internal
 */
class UserUpdateProcessorTest extends TestCase {
    private UserUpdateProcessor $processor;
    private MockObject|UserPasswordHasherInterface $userPasswordHasher;
    private User $user;

    /**
     * @throws \ReflectionException
     */
    protected function setUp(): void {
        $this->user = new User();

        $this->userPasswordHasher = $this->createMock(UserPasswordHasher::class);
        $decoratedProcessor = $this->createMock(ProcessorInterface::class);
        $this->processor = new UserUpdateProcessor(
            $decoratedProcessor,
            $this->userPasswordHasher,
        );
    }

    public function testHashesPasswordBeforeUpdate() {
        // given
        $this->user->plainPassword = 'test plain password';
        $this->userPasswordHasher->expects($this->once())->method('hashPassword')->willReturn('test hash');

        // when
        /** @var User $data */
        $data = $this->processor->onBefore($this->user, new Patch());

        // then
        $this->assertEquals('test hash', $data->password);
        $this->assertNull($data->plainPassword);
    }
}
