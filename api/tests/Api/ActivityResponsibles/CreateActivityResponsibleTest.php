<?php

namespace App\Tests\Api\ActivityResponsibles;

use ApiPlatform\Core\Api\OperationType;
use App\Entity\ActivityResponsible;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CreateActivityResponsibleTest extends ECampApiTestCase {
    // TODO input filter tests
    // TODO validation tests

    public function testCreateActivityResponsibleIsDeniedForAnonymousUser() {
        static::createClient()->request('POST', '/activity_responsibles', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testCreateActivityResponsibleIsNotPossibleForUnrelatedUserBecauseActivityIsNotReadable() {
        static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->username])
            ->request('POST', '/activity_responsibles', ['json' => $this->getExampleWritePayload()])
        ;
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('activity2').'".',
        ]);
    }

    public function testCreateActivityResponsibleIsNotPossibleForInactiveCollaboratorBecauseActivityIsNotReadable() {
        static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->username])
            ->request('POST', '/activity_responsibles', ['json' => $this->getExampleWritePayload()])
        ;
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('activity2').'".',
        ]);
    }

    public function testCreateActivityResponsibleIsDeniedForGuest() {
        static::createClientWithCredentials(['username' => static::$fixtures['user3guest']->username])
            ->request('POST', '/activity_responsibles', ['json' => $this->getExampleWritePayload()])
    ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testCreateActivityResponsibleIsAllowedForMember() {
        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->username])
            ->request('POST', '/activity_responsibles', ['json' => $this->getExampleWritePayload()])
    ;

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload());
    }

    public function testCreateActivityResponsibleIsAllowedForManager() {
        static::createClientWithCredentials()->request('POST', '/activity_responsibles', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload());
    }

    public function testCreateActivityResponsibleInCampPrototypeIsDeniedForUnrelatedUser() {
        static::createClientWithCredentials()->request('POST', '/activity_responsibles', ['json' => $this->getExampleWritePayload([
            'activity' => $this->getIriFor('activity1campPrototype'),
        ])]);

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testCreateActivityResponsibleValidatesMissingActivity() {
        static::createClientWithCredentials()->request('POST', '/activity_responsibles', ['json' => $this->getExampleWritePayload([], ['activity'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'activity',
                    'message' => 'This value should not be null.',
                ],
            ],
        ]);
    }

    public function testCreateActivityResponsibleValidatesMissingCampCollaboration() {
        static::createClientWithCredentials()->request('POST', '/activity_responsibles', ['json' => $this->getExampleWritePayload([], ['campCollaboration'])]);

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

    public function testValidatesDuplicateActivityResponsible() {
        static::createClientWithCredentials()->request('POST', '/activity_responsibles', ['json' => $this->getExampleWritePayload([
            'activity' => $this->getIriFor(static::$fixtures['activityResponsible1']->activity),
            'campCollaboration' => $this->getIriFor(static::$fixtures['activityResponsible1']->campCollaboration),
        ])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'activity',
                    'message' => 'This value is already used.',
                ],
            ],
        ]);
    }

    public function getExampleWritePayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            ActivityResponsible::class,
            OperationType::COLLECTION,
            'post',
            array_merge([
                'activity' => $this->getIriFor('activity2'),
                'campCollaboration' => $this->getIriFor('campCollaboration1manager'),
            ], $attributes),
            [],
            $except
        );
    }

    public function getExampleReadPayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            ActivityResponsible::class,
            OperationType::ITEM,
            'get',
            $attributes,
            ['activity', 'campCollaboration'],
            $except
        );
    }
}
