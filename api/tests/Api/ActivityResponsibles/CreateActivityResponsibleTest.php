<?php

namespace App\Tests\Api\ActivityResponsibles;

use App\Entity\ActivityResponsible;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CreateActivityResponsibleTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator
    // TODO input filter tests
    // TODO validation tests

    public function testCreateActivityResponsibleIsAllowedForCollaborator() {
        static::createClientWithCredentials()->request('POST', '/activity_responsibles', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload());
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
            array_merge([
                'activity' => $this->getIriFor('activity2'),
                'campCollaboration' => $this->getIriFor('campCollaboration1'),
            ], $attributes),
            [],
            $except
        );
    }

    public function getExampleReadPayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            ActivityResponsible::class,
            $attributes,
            ['activity', 'campCollaboration'],
            $except
        );
    }
}
