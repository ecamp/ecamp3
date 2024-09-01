<?php

namespace App\Tests\Api\Checklists;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class UpdateChecklistTest extends ECampApiTestCase {
    public function testPatchChecklistIsDeniedForAnonymousUser() {
        $checklist = static::getFixture('checklist1');
        static::createBasicClient()->request('PATCH', '/checklists/'.$checklist->getId(), ['json' => [
            'name' => 'ChecklistName',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testPatchChecklistIsDeniedForUnrelatedUser() {
        $checklist = static::getFixture('checklist1');
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('PATCH', '/checklists/'.$checklist->getId(), ['json' => [
                'name' => 'ChecklistName',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testPatchChecklistIsDeniedForInactiveCollaborator() {
        $checklist = static::getFixture('checklist1');
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('PATCH', '/checklists/'.$checklist->getId(), ['json' => [
                'name' => 'ChecklistName',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testPatchChecklistIsDeniedForGuest() {
        $checklist = static::getFixture('checklist1');
        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
            ->request('PATCH', '/checklists/'.$checklist->getId(), ['json' => [
                'name' => 'ChecklistName',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testPatchChecklistIsAllowedForMember() {
        $checklist = static::getFixture('checklist1');
        $response = static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('PATCH', '/checklists/'.$checklist->getId(), ['json' => [
                'name' => 'ChecklistName',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'name' => 'ChecklistName',
        ]);
    }

    public function testPatchChecklistIsAllowedForManager() {
        $checklist = static::getFixture('checklist1');
        $response = static::createClientWithCredentials()->request('PATCH', '/checklists/'.$checklist->getId(), ['json' => [
            'name' => 'ChecklistName',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'name' => 'ChecklistName',
        ]);
    }

    public function testPatchChecklistInCampPrototypeIsDeniedForUnrelatedUser() {
        $checklist = static::getFixture('checklist1campPrototype');
        $response = static::createClientWithCredentials()->request('PATCH', '/checklists/'.$checklist->getId(), ['json' => [
            'name' => 'ChecklistName',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testPatchChecklistDisallowsChangingCamp() {
        $checklist = static::getFixture('checklist1');
        static::createClientWithCredentials()->request('PATCH', '/checklists/'.$checklist->getId(), ['json' => [
            'camp' => $this->getIriFor('camp2'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("camp" is unknown).',
        ]);
    }

    public function testPatchChecklistValidatesNullName() {
        $checklist = static::getFixture('checklist1');
        static::createClientWithCredentials()->request(
            'PATCH',
            '/checklists/'.$checklist->getId(),
            [
                'json' => [
                    'name' => null,
                ],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ]
        );

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'The type of the "name" attribute must be "string", "NULL" given.',
        ]);
    }

    public function testPatchChecklistValidatesBlankName() {
        $checklist = static::getFixture('checklist1');
        static::createClientWithCredentials()->request(
            'PATCH',
            '/checklists/'.$checklist->getId(),
            [
                'json' => [
                    'name' => ' ',
                ],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'name',
                    'message' => 'This value should not be blank.',
                ],
            ],
        ]);
    }

    public function testPatchChecklistValidatesTooLongName() {
        $checklist = static::getFixture('checklist1');
        static::createClientWithCredentials()->request(
            'PATCH',
            '/checklists/'.$checklist->getId(),
            [
                'json' => [
                    'name' => str_repeat('l', 33),
                ],
                'headers' => ['Content-Type' => 'application/merge-patch+json'],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'name',
                    'message' => 'This value is too long. It should have 32 characters or less.',
                ],
            ],
        ]);
    }

    public function testPatchChecklistTrimsName() {
        $checklist = static::getFixture('checklist1');
        static::createClientWithCredentials()->request(
            'PATCH',
            '/checklists/'.$checklist->getId(),
            [
                'json' => [
                    'name' => "  \t ChecklistName\t ",
                ],
                'headers' => ['Content-Type' => 'application/merge-patch+json'], ]
        );
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(
            [
                'name' => 'ChecklistName',
            ]
        );
    }

    public function testPatchChecklistCleansForbiddenCharactersFromName() {
        $checklist = static::getFixture('checklist1');
        $client = static::createClientWithCredentials();
        $client->disableReboot();
        $client->request(
            'PATCH',
            '/checklists/'.$checklist->getId(),
            [
                'json' => [
                    'name' => "<b>Checklist</b>Name\n\t<a>",
                ],
                'headers' => ['Content-Type' => 'application/merge-patch+json'], ]
        );
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(
            [
                'name' => '<b>Checklist</b>Name<a>',
            ]
        );
    }
}
