<?php

namespace App\Tests\Api\Periods;

use App\Entity\Period;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ReadPeriodTest extends ECampApiTestCase {
    public function testGetSinglePeriodIsDeniedToAnonymousUser() {
        /** @var Period $period */
        $period = static::$fixtures['period1'];
        static::createClient()->request('GET', '/periods/'.$period->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testGetSinglePeriodIsDeniedForUnrelatedUser() {
        /** @var Period $period */
        $period = static::$fixtures['period1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->username])
            ->request('GET', '/periods/'.$period->getId())
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testGetSinglePeriodIsDeniedForInactiveCollaborator() {
        /** @var Period $period */
        $period = static::$fixtures['period1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->username])
            ->request('GET', '/periods/'.$period->getId())
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testGetSinglePeriodIsAllowedForGuest() {
        /** @var Period $period */
        $period = static::$fixtures['period1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user3guest']->username])
            ->request('GET', '/periods/'.$period->getId())
        ;

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $period->getId(),
            'description' => $period->description,
            'start' => $period->start->format('Y-m-d'),
            'end' => $period->end->format('Y-m-d'),
            '_links' => [
                'camp' => ['href' => $this->getIriFor('camp1')],
                'materialItems' => ['href' => '/material_items?period=/periods/'.$period->getId()],
                'days' => ['href' => '/days?period=/periods/'.$period->getId()],
                'scheduleEntries' => ['href' => '/schedule_entries?period=/periods/'.$period->getId()],
            ],
        ]);
    }

    public function testGetSinglePeriodIsAllowedForMember() {
        /** @var Period $period */
        $period = static::$fixtures['period1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->username])
            ->request('GET', '/periods/'.$period->getId())
        ;

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $period->getId(),
            'description' => $period->description,
            'start' => $period->start->format('Y-m-d'),
            'end' => $period->end->format('Y-m-d'),
            '_links' => [
                'camp' => ['href' => $this->getIriFor('camp1')],
                'materialItems' => ['href' => '/material_items?period=/periods/'.$period->getId()],
                'days' => ['href' => '/days?period=/periods/'.$period->getId()],
                'scheduleEntries' => ['href' => '/schedule_entries?period=/periods/'.$period->getId()],
            ],
        ]);
    }

    public function testGetSinglePeriodIsAllowedForManager() {
        /** @var Period $period */
        $period = static::$fixtures['period1'];
        static::createClientWithCredentials()->request('GET', '/periods/'.$period->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $period->getId(),
            'description' => $period->description,
            'start' => $period->start->format('Y-m-d'),
            'end' => $period->end->format('Y-m-d'),
            '_links' => [
                'camp' => ['href' => $this->getIriFor('camp1')],
                'materialItems' => ['href' => '/material_items?period=/periods/'.$period->getId()],
                'days' => ['href' => '/days?period=/periods/'.$period->getId()],
                'scheduleEntries' => ['href' => '/schedule_entries?period=/periods/'.$period->getId()],
            ],
        ]);
    }

    public function testGetSinglePeriodFromCampPrototypeIsAllowedForUnrelatedUser() {
        /** @var Period $period */
        $period = static::$fixtures['period1campPrototype'];

        // Precondition: no collaborations on the camp.
        // This is to make sure a left join from camp to collaborations is used.
        $this->assertEmpty($period->camp->collaborations);

        static::createClientWithCredentials()->request('GET', '/periods/'.$period->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $period->getId(),
            'description' => $period->description,
            'start' => $period->start->format('Y-m-d'),
            'end' => $period->end->format('Y-m-d'),
            '_links' => [
                'camp' => ['href' => $this->getIriFor('campPrototype')],
            ],
        ]);
    }
}
