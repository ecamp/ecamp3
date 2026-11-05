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

    public function testDeletePeriodPurgesCacheTags() {
        $client = static::createClientWithCredentials();
        $cacheManager = $this->mockCacheManager();

        $period = static::getFixture('period1');
        $client->request('DELETE', '/periods/'.$period->getId());

        $this->assertResponseStatusCodeSame(204);

        $camp = static::getFixture('camp1');
        $campCollaboration = static::getFixture('campCollaboration1manager');
        $day1 = static::getFixture('day1period1');
        $day2 = static::getFixture('day2period1');
        $day3 = static::getFixture('day3period1');
        $dayResponsible1 = static::getFixture('dayResponsible1');
        $dayResponsible2 = static::getFixture('dayResponsible1day2period1');
        self::assertEqualsCanonicalizing([
            '/periods',
            '/days',
            '/day_responsibles',
            '/periods/'.$period->getId().'/days',
            '/days/'.$day1->getId().'/day_responsibles',
            '/days/'.$day2->getId().'/day_responsibles',
            $period->getId(),
            $period->getId().'#days',
            $camp->getId().'#periods',
            $camp->getId().'^dayNumbers',
            $campCollaboration->getId().'#dayResponsibles',
            $day1->getId(),
            $day1->getId().'#period',
            $day1->getId().'#dayResponsibles',
            $day2->getId(),
            $day2->getId().'#period',
            $day2->getId().'#dayResponsibles',
            $day3->getId(),
            $day3->getId().'#period',
            $dayResponsible1->getId(),
            $dayResponsible1->getId().'#day',
            $dayResponsible2->getId(),
            $dayResponsible2->getId().'#day',
            static::getFixture('scheduleEntry1')->getId().'#period',
            static::getFixture('scheduleEntry2')->getId().'#period',
            static::getFixture('scheduleEntry1period1camp1')->getId().'#period',
            static::getFixture('materialItem1period1')->getId().'#period',
        ], $cacheManager->getInvalidatedTags());
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

    public function testDeletePeriodFromSharedCampIsDeniedForUnrelatedUser() {
        $period = static::getFixture('period1campShared');
        static::createClientWithCredentials()->request('DELETE', '/periods/'.$period->getId());

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeletePeriodFromSharedCampIsDeniedForInactiveUser() {
        $period = static::getFixture('period1campShared');
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('DELETE', '/periods/'.$period->getId())
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeletePeriodFromSharedCampIsDeniedForInvitedUser() {
        $period = static::getFixture('period1campShared');
        static::createClientWithCredentials(['email' => static::$fixtures['user6invited']->getEmail()])
            ->request('DELETE', '/periods/'.$period->getId())
        ;

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
