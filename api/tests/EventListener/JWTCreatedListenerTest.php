<?php

namespace App\Tests\EventListener;

use ApiPlatform\Api\IriConverterInterface;
use App\Entity\User;
use App\EventListener\JWTCreatedListener;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Security;

/**
 * @internal
 */
class JWTCreatedListenerTest extends TestCase {
    /**
     * @var JWTCreatedListener
     */
    private $jwtCreatedListener;

    /**
     * @var MockObject|Security
     */
    private $security;

    /**
     * @var IriConverterInterface|MockObject
     */
    private $iriConverter;

    public function setUp(): void {
        parent::setUp();

        $this->security = $this->createMock(Security::class);
        $this->iriConverter = $this->createMock(IriConverterInterface::class);

        $this->jwtCreatedListener = new JWTCreatedListener($this->security, $this->iriConverter);
    }

    public function testOnJWTCreatedAddsUserURIToPayload() {
        // given
        $event = $this->createMock(JWTCreatedEvent::class);
        $this->security->expects($this->once())->method('getUser')->willReturn(new User());
        $this->iriConverter->expects($this->once())->method('getIriFromResource')->willReturn('/users/1a2b3c4dtest');

        // then
        $event->expects($this->once())
            ->method('setData')
            ->with(['user' => '/users/1a2b3c4dtest'])
        ;

        // when
        $this->jwtCreatedListener->onJWTCreated($event);
    }

    public function testOnJWTCreatedDoesNothingIfNoUserIsFound() {
        // given
        $event = $this->createMock(JWTCreatedEvent::class);
        $this->security->expects($this->once())->method('getUser')->willReturn(null);

        // then
        $event->expects($this->never())->method('setData');

        // when
        $this->jwtCreatedListener->onJWTCreated($event);
    }
}
