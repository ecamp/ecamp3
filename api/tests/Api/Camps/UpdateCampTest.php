<?php

namespace App\Tests\Api\Camps;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class UpdateCampTest extends ECampApiTestCase {
    public function testPatchCampIsDeniedForAnonymousUser() {
        $camp = static::$fixtures['camp1'];
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
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user4unrelated']->getUsername()])
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
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user5inactive']->getUsername()])
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
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user3guest']->username])
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
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials(['username' => static::$fixtures['user2member']->username])
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
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'title' => 'Hello World',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'title' => 'Hello World',
        ]);
    }

    public function testPatchPrototypeCampIsDeniedForUnrelatedUser() {
        $camp = static::$fixtures['campPrototype'];
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'title' => 'Hello World',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testPatchCampDisallowsEditingPeriods() {
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'periods' => [],
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("periods" is unknown).',
        ]);
    }

    public function testPatchCampTrimsName() {
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'name' => " So-La\t ",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'name' => 'So-La',
        ]);
    }

    public function testPatchCampCleansHTMLFromName() {
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'name' => 'So-<script>alert(1)</script>La',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'name' => 'So-La',
        ]);
    }

    public function testPatchCampValidatesBlankName() {
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'name' => '',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

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

    public function testPatchCampValidatesLongName() {
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'name' => 'A very long camp name which is not really useful',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

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

    public function testPatchCampTrimsTitle() {
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'title' => " Sommerlager\t ",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'title' => 'Sommerlager',
        ]);
    }

    public function testPatchCampCleansHTMLFromTitle() {
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'title' => 'Sommer<script>alert(1)</script>lager',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'title' => 'Sommerlager',
        ]);
    }

    public function testPatchCampValidatesBlankTitle() {
        $camp = static::$fixtures['camp1'];
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
        $camp = static::$fixtures['camp1'];
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
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'motto' => " Dschungelbuch\t ",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'motto' => 'Dschungelbuch',
        ]);
    }

    public function testPatchCampCleansHTMLFromMotto() {
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'motto' => 'Dschungel<script>alert(1)</script>buch',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'motto' => 'Dschungelbuch',
        ]);
    }

    public function testPatchCampAllowsNullMotto() {
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'motto' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'motto' => null,
        ]);
    }

    public function testPatchCampAllowsEmptyMotto() {
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'motto' => '',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'motto' => '',
        ]);
    }

    public function testPatchCampValidatesLongMotto() {
        $camp = static::$fixtures['camp1'];
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
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'addressName' => " Auf dem Hügel\t ",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'addressName' => 'Auf dem Hügel',
        ]);
    }

    public function testPatchCampCleansHTMLFromAddressName() {
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'addressName' => 'Auf dem Hügel<script>alert(1)</script>',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'addressName' => 'Auf dem Hügel',
        ]);
    }

    public function testPatchCampAllowsNullAddressName() {
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'addressName' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressName' => null,
        ]);
    }

    public function testPatchCampAllowsEmptyAddressName() {
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'addressName' => '',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressName' => '',
        ]);
    }

    public function testPatchCampValidatesLongAddressName() {
        $camp = static::$fixtures['camp1'];
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
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'addressStreet' => " Suppenstrasse 123a\t ",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'addressStreet' => 'Suppenstrasse 123a',
        ]);
    }

    public function testPatchCampCleansHTMLFromAddressStreet() {
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'addressStreet' => 'Suppenstrasse <script>alert(1)</script>123a',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'addressStreet' => 'Suppenstrasse 123a',
        ]);
    }

    public function testPatchCampAllowsNullAddressStreet() {
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'addressStreet' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressStreet' => null,
        ]);
    }

    public function testPatchCampAllowsEmptyAddressStreet() {
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'addressStreet' => '',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressStreet' => '',
        ]);
    }

    public function testPatchCampValidatesLongAddressStreet() {
        $camp = static::$fixtures['camp1'];
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
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'addressZipcode' => " 8000\t ",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'addressZipcode' => '8000',
        ]);
    }

    public function testPatchCampCleansHTMLFromAddressZipcode() {
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'addressZipcode' => '800<script>alert(1)</script>0',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'addressZipcode' => '8000',
        ]);
    }

    public function testPatchCampAllowsNullAddressZipcode() {
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'addressZipcode' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressZipcode' => null,
        ]);
    }

    public function testPatchCampAllowsEmptyAddressZipcode() {
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'addressZipcode' => '',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressZipcode' => '',
        ]);
    }

    public function testPatchCampValidatesLongAddressZipcode() {
        $camp = static::$fixtures['camp1'];
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
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'addressZipcode' => " Unterberg\t ",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'addressZipcode' => 'Unterberg',
        ]);
    }

    public function testPatchCampCleansHTMLFromAddressCity() {
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'addressCity' => 'Unter<script>alert(1)</script>berg',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'addressCity' => 'Unterberg',
        ]);
    }

    public function testPatchCampAllowsNullAddressCity() {
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'addressCity' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressCity' => null,
        ]);
    }

    public function testPatchCampAllowsEmptyAddressCity() {
        $camp = static::$fixtures['camp1'];
        static::createClientWithCredentials()->request('PATCH', '/camps/'.$camp->getId(), ['json' => [
            'addressCity' => '',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressCity' => '',
        ]);
    }

    public function testPatchCampValidatesLongAddressCity() {
        $camp = static::$fixtures['camp1'];
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
