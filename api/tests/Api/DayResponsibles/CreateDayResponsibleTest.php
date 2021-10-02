<?php

namespace App\Tests\Api\DayResponsibles;

use ApiPlatform\Core\Api\OperationType;
use App\Entity\DayResponsible;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CreateDayResponsibleTest extends ECampApiTestCase {
    // TODO input filter tests
    // TODO validation tests

    public function testCreateDayResponsibleIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('POST', '/day_responsibles', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testCreateDayResponsibleIsNotPossibleForUnrelatedUserBecauseDayIsNotReadable() {
        static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->username])
            ->request('POST', '/day_responsibles', ['json' => $this->getExampleWritePayload()])
        ;
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('day3period1').'".',
        ]);
    }

    public function testCreateDayResponsibleIsNotPossibleForInactiveCollaboratorBecauseDayIsNotReadable() {
        static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->username])
            ->request('POST', '/day_responsibles', ['json' => $this->getExampleWritePayload()])
        ;
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('day3period1').'".',
        ]);
    }

    public function testCreateDayResponsibleIsDeniedForGuest() {
        static::createClientWithCredentials(['username' => static::$fixtures['user3guest']->username])
            ->request('POST', '/day_responsibles', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testCreateDayResponsibleIsAllowedForMember() {
        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->username])
            ->request('POST', '/day_responsibles', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload());
    }

    public function testCreateDayResponsibleIsAllowedForManager() {
        static::createClientWithCredentials()->request('POST', '/day_responsibles', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload());
    }

    public function testCreateDayResponsibleInCampPrototypeIsDeniedForUnrelatedUser() {
        static::createClientWithCredentials()->request('POST', '/day_responsibles', ['json' => $this->getExampleWritePayload([
            'day' => $this->getIriFor('day1period1campPrototype'),
            'campCollaboration' => $this->getIriFor('campCollaboration1campPrototype'),
        ])]);

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testCreateDayResponsibleValidatesMissingDay() {
        static::createClientWithCredentials()->request('POST', '/day_responsibles', ['json' => $this->getExampleWritePayload([], ['day'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'day',
                    'message' => 'This value should not be null.',
                ],
            ],
        ]);
    }

    public function testCreateDayResponsibleValidatesMissingCampCollaboration() {
        static::createClientWithCredentials()->request('POST', '/day_responsibles', ['json' => $this->getExampleWritePayload([], ['campCollaboration'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'campCollaboration',
                    'message' => 'This value should not be null.',
                ],
            ],
        ]);
    }

    public function testValidatesDuplicateDayResponsible() {
        static::createClientWithCredentials()->request('POST', '/day_responsibles', ['json' => $this->getExampleWritePayload([
            'day' => $this->getIriFor(static::$fixtures['dayResponsible1']->day),
            'campCollaboration' => $this->getIriFor(static::$fixtures['dayResponsible1']->campCollaboration),
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'day',
                    'message' => 'This value is already used.',
                ],
            ],
        ]);
    }

    public function getExampleWritePayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            DayResponsible::class,
            OperationType::COLLECTION,
            'post',
            array_merge([
                'day' => $this->getIriFor('day3period1'),
                'campCollaboration' => $this->getIriFor('campCollaboration1manager'),
            ], $attributes),
            [],
            $except
        );
    }

    public function getExampleReadPayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            DayResponsible::class,
            OperationType::ITEM,
            'get',
            $attributes,
            ['day', 'campCollaboration'],
            $except
        );
    }
}
