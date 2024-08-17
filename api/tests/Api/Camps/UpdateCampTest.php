<?php

namespace App\Tests\Api\Camps;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class UpdateCampTest extends ECampApiTestCase {
    public function testPatchCampIsDeniedForAnonymousUser() {
        $camp = static::getFixture('camp1');
        static::createBasicClient()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'title' => 'Hello World',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testPatchCampIsDeniedForUnrelatedUser() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
                'title' => 'Hello World',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testPatchCampIsDeniedForInactiveCollaborator() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
                'title' => 'Hello World',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testPatchCampIsDeniedForGuest() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
            ->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
                'title' => 'Hello World',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testPatchCampIsAllowedForMember() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
                'title' => 'Hello World',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'title' => 'Hello World',
        ]);
    }

    public function testPatchCampIsAllowedForManager() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'title' => 'Hello World',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'title' => 'Hello World',
        ]);
    }

    public function testPatchPrototypeCampIsDeniedForUnrelatedUser() {
        $camp = static::getFixture('campPrototype');
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'title' => 'Hello World',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testPatchCampDisallowsEditingPeriods() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'periods' => [],
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("periods" is unknown).',
        ]);
    }

    public function testPatchCampDisallowsSettingIsPrototype() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'isPrototype' => true,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("isPrototype" is unknown).',
        ]);
    }

    public function testPatchCampTrimsName() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'shortTitle' => " So-La\t ",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'shortTitle' => 'So-La',
        ]);
    }

    public function testPatchCampCleansForbiddenCharactersFromName() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'shortTitle' => "So-\n\tLa",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'shortTitle' => 'So-La',
        ]);
    }

    public function testPatchCampValidatesLongName() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'shortTitle' => 'A very long camp name which is not really useful',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'shortTitle',
                    'message' => 'This value is too long. It should have 32 characters or less.',
                ],
            ],
        ]);
    }

    public function testPatchCampTrimsTitle() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'title' => " Sommerlager\t ",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'title' => 'Sommerlager',
        ]);
    }

    public function testPatchCampCleansForbiddenCharactersFromTitle() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'title' => "Sommer\n\tlager",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'title' => 'Sommerlager',
        ]);
    }

    public function testPatchCampValidatesBlankTitle() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'title' => '',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'title',
                    'message' => 'This value should not be blank.',
                ],
            ],
        ]);
    }

    public function testPatchCampValidatesLongTitle() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'title' => 'A very long camp title which is not really useful',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'title',
                    'message' => 'This value is too long. It should have 32 characters or less.',
                ],
            ],
        ]);
    }

    public function testPatchCampTrimsMotto() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'motto' => " Dschungelbuch\t ",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'motto' => 'Dschungelbuch',
        ]);
    }

    public function testPatchCampCleansForbiddenCharactersFromMotto() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'motto' => "this\n\t\u{202E} is 'a' <sample> text😀 \\",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'motto' => "this is 'a' <sample> text😀 \\",
        ]);
    }

    public function testPatchCampAllowsNullMotto() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'motto' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'motto' => null,
        ]);
    }

    public function testPatchCampAllowsEmptyMotto() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'motto' => '',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'motto' => '',
        ]);
    }

    public function testPatchCampValidatesLongMotto() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'motto' => 'This camp has an extremely long motto. This camp has an extremely long motto. This camp has an extremely long motto. This camp ha',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'motto',
                    'message' => 'This value is too long. It should have 128 characters or less.',
                ],
            ],
        ]);
    }

    public function testPatchCampTrimsAddressName() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'addressName' => " Auf dem Hügel\t ",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'addressName' => 'Auf dem Hügel',
        ]);
    }

    public function testPatchCampCleansForbiddenCharactersFromAddressName() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'addressName' => "Auf dem Hügel\n\t",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'addressName' => 'Auf dem Hügel',
        ]);
    }

    public function testPatchCampAllowsNullAddressName() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'addressName' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressName' => null,
        ]);
    }

    public function testPatchCampAllowsEmptyAddressName() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'addressName' => '',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressName' => '',
        ]);
    }

    public function testPatchCampValidatesLongAddressName() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'addressName' => 'This camp has an extremely long address. This camp has an extremely long address. This camp has an extremely long address. This!!',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'addressName',
                    'message' => 'This value is too long. It should have 128 characters or less.',
                ],
            ],
        ]);
    }

    public function testPatchCampTrimsAddressStreet() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'addressStreet' => " Suppenstrasse 123a\t ",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'addressStreet' => 'Suppenstrasse 123a',
        ]);
    }

    public function testPatchCampCleansForbiddenCharactersFromAddressStreet() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'addressStreet' => "Suppenstrasse \n\t123a",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'addressStreet' => 'Suppenstrasse 123a',
        ]);
    }

    public function testPatchCampAllowsNullAddressStreet() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'addressStreet' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressStreet' => null,
        ]);
    }

    public function testPatchCampAllowsEmptyAddressStreet() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'addressStreet' => '',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressStreet' => '',
        ]);
    }

    public function testPatchCampValidatesLongAddressStreet() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'addressStreet' => 'This camp has an extremely long address. This camp has an extremely long address. This camp has an extremely long address. This!!',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'addressStreet',
                    'message' => 'This value is too long. It should have 128 characters or less.',
                ],
            ],
        ]);
    }

    public function testPatchCampTrimsAddressZipcode() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'addressZipcode' => " 8000\t ",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'addressZipcode' => '8000',
        ]);
    }

    public function testPatchCampCleansForbiddenCharactersFromAddressZipcode() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'addressZipcode' => "800\n\t0",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'addressZipcode' => '8000',
        ]);
    }

    public function testPatchCampAllowsNullAddressZipcode() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'addressZipcode' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressZipcode' => null,
        ]);
    }

    public function testPatchCampAllowsEmptyAddressZipcode() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'addressZipcode' => '',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressZipcode' => '',
        ]);
    }

    public function testPatchCampValidatesLongAddressZipcode() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'addressZipcode' => 'This camp has an extremely long zipcode. This camp has an extremely long zipcode. This camp has an extremely long zipcode. This!!',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'addressZipcode',
                    'message' => 'This value is too long. It should have 128 characters or less.',
                ],
            ],
        ]);
    }

    public function testPatchCampTrimsAddressCity() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'addressZipcode' => " Unterberg\t ",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'addressZipcode' => 'Unterberg',
        ]);
    }

    public function testPatchCampCleansForbiddenCharactersFromAddressCity() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'addressCity' => "Unter\n\tberg",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'addressCity' => 'Unterberg',
        ]);
    }

    public function testPatchCampAllowsNullAddressCity() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'addressCity' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressCity' => null,
        ]);
    }

    public function testPatchCampAllowsEmptyAddressCity() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'addressCity' => '',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressCity' => '',
        ]);
    }

    public function testPatchCampValidatesLongAddressCity() {
        $camp = static::getFixture('camp1');
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'addressCity' => 'This camp has an extremely long city. This camp has an extremely long city. This camp has an extremely long city. This camp, I\'m telling you!',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'addressCity',
                    'message' => 'This value is too long. It should have 128 characters or less.',
                ],
            ],
        ]);
    }
}
