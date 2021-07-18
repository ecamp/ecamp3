<?php

namespace App\Tests\Api\DayResponsibles;

use App\Entity\DayResponsible;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CreateDayResponsibleTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator
    // TODO input filter tests
    // TODO validation tests

    public function testCreateDayResponsibleIsAllowedForCollaborator() {
        static::createClientWithCredentials()->request('POST', '/day_responsibles', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload());
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
            array_merge([
                'day' => $this->getIriFor('day3period1'),
                'campCollaboration' => $this->getIriFor('campCollaboration1'),
            ], $attributes),
            [],
            $except
        );
    }

    public function getExampleReadPayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            DayResponsible::class,
            $attributes,
            ['day', 'campCollaboration'],
            $except
        );
    }
}
