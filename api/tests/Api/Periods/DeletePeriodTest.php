<?php

namespace App\Tests\Api\Periods;

use App\Entity\Period;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeletePeriodTest extends ECampApiTestCase {
    public function testDeletePeriodIsDeniedForAnonymousUser() {
        $period = static::getFixture('period1');
        static::createBasicClient()->request('DELETE', '/periods/'.$period->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testDeletePeriodIsDeniedForUnrelatedUser() {
        $period = static::getFixture('period1');
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('DELETE', '/periods/'.$period->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testDeletePeriodIsDeniedForInactiveCollaborator() {
        $period = static::getFixture('period1');
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('DELETE', '/periods/'.$period->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testDeletePeriodIsDeniedForGuest() {
        $period = static::getFixture('period1');
        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
            ->request('DELETE', '/periods/'.$period->getId())
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeletePeriodIsAllowedForMember() {
        $period = static::getFixture('period1');
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('DELETE', '/periods/'.$period->getId())
        ;
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(Period::class)->find($period->getId()));
    }

    public function testDeletePeriodIsAllowedForManager() {
        $period = static::getFixture('period1');
        static::createClientWithCredentials()->request('DELETE', '/periods/'.$period->getId());
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(Period::class)->find($period->getId()));
    }

    public function testDeletePeriodFromCampPrototypeIsDeniedForUnrelatedUser() {
        $period = static::getFixture('period1campPrototype');
        static::createClientWithCredentials()->request('DELETE', '/periods/'.$period->getId());

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeleteLastPeriodIsRejected() {
        $period = static::getFixture('period1camp2');
        static::createClientWithCredentials()
            ->request('DELETE', '/periods/'.$period->getId())
        ;
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'camp.periods',
                    'message' => 'A camp must have at least one period.',
                ],
            ],
        ]);
    }
}
