<?php

namespace App\Tests\Api\Camps;

use App\Entity\Camp;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CreateCampTest extends ECampApiTestCase {
    public function testCreateCampWhenNotLoggedIn() {
        static::createClient()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class)]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testCreateCampWhenLoggedIn() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class)]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExamplePayload(Camp::class, [
            'isPrototype' => false,
        ], ['periods']));
    }

    public function testCreateCampDoesntExposeCampPrototypeId() {
        $response = static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class)]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertArrayNotHasKey('campPrototypeId', $response->toArray());
    }

    public function testCreateCampSetsCreatorToAuthenticatedUser() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class)]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['_links' => [
            'creator' => ['href' => '/users/'.static::$fixtures['user1']->getId()],
        ]]);
    }

    public function testCreateCampSetsOwnerToAuthenticatedUser() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class)]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['_links' => [
            'owner' => ['href' => '/users/'.static::$fixtures['user1']->getId()],
        ]]);
    }

    public function testCreateCampTrimsName() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [
            'name' => " So-La\t ",
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExamplePayload(Camp::class, [
            'name' => 'So-La',
        ], ['periods']));
    }

    public function testCreateCampCleansHTMLFromName() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [
            'name' => 'So-<script>alert(1)</script>La',
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExamplePayload(Camp::class, [
            'name' => 'So-La',
        ], ['periods']));
    }

    public function testCreateCampValidatesMissingName() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [], ['name'])]);

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

    public function testCreateCampValidatesBlankName() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [
            'name' => '',
        ])]);

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

    public function testCreateCampValidatesLongName() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [
            'name' => 'A very long camp name which is not really useful',
        ])]);

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

    public function testCreateCampTrimsTitle() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [
            'title' => " Sommerlager\t ",
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExamplePayload(Camp::class, [
            'title' => 'Sommerlager',
        ], ['periods']));
    }

    public function testCreateCampCleansHTMLFromTitle() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [
            'title' => 'Sommer<script>alert(1)</script>lager',
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExamplePayload(Camp::class, [
            'title' => 'Sommerlager',
        ], ['periods']));
    }

    public function testCreateCampValidatesMissingTitle() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [], ['title'])]);

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

    public function testCreateCampValidatesBlankTitle() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [
            'title' => '',
        ])]);

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

    public function testCreateCampValidatesLongTitle() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [
            'title' => 'A very long camp title which is not really useful',
        ])]);

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

    public function testCreateCampTrimsMotto() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [
            'motto' => " Dschungelbuch\t ",
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExamplePayload(Camp::class, [
            'motto' => 'Dschungelbuch',
        ], ['periods']));
    }

    public function testCreateCampCleansHTMLFromMotto() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [
            'motto' => 'Dschungel<script>alert(1)</script>buch',
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExamplePayload(Camp::class, [
            'motto' => 'Dschungelbuch',
        ], ['periods']));
    }

    public function testCreateCampAllowsMissingMotto() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [], ['motto'])]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'motto' => null,
        ]);
    }

    public function testCreateCampAllowsNullMotto() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [
            'motto' => null,
        ])]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'motto' => null,
        ]);
    }

    public function testCreateCampAllowsEmptyMotto() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [
            'motto' => '',
        ])]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'motto' => '',
        ]);
    }

    public function testCreateCampValidatesLongMotto() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [
            'motto' => 'This camp has an extremely long motto. This camp has an extremely long motto. This camp has an extremely long motto. This camp ha',
        ])]);

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

    public function testCreateCampTrimsAddressName() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [
            'addressName' => " Auf dem Hügel\t ",
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExamplePayload(Camp::class, [
            'addressName' => 'Auf dem Hügel',
        ], ['periods']));
    }

    public function testCreateCampCleansHTMLFromAddressName() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [
            'addressName' => 'Auf dem Hügel<script>alert(1)</script>',
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExamplePayload(Camp::class, [
            'addressName' => 'Auf dem Hügel',
        ], ['periods']));
    }

    public function testCreateCampAllowsMissingAddressName() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [], ['addressName'])]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressName' => null,
        ]);
    }

    public function testCreateCampAllowsNullAddressName() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [
            'addressName' => null,
        ])]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressName' => null,
        ]);
    }

    public function testCreateCampAllowsEmptyAddressName() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [
            'addressName' => '',
        ])]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressName' => '',
        ]);
    }

    public function testCreateCampValidatesLongAddressName() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [
            'addressName' => 'This camp has an extremely long address. This camp has an extremely long address. This camp has an extremely long address. This!!',
        ])]);

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

    public function testCreateCampTrimsAddressStreet() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [
            'addressStreet' => " Suppenstrasse 123a\t ",
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExamplePayload(Camp::class, [
            'addressStreet' => 'Suppenstrasse 123a',
        ], ['periods']));
    }

    public function testCreateCampCleansHTMLFromAddressStreet() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [
            'addressStreet' => 'Suppenstrasse <script>alert(1)</script>123a',
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExamplePayload(Camp::class, [
            'addressStreet' => 'Suppenstrasse 123a',
        ], ['periods']));
    }

    public function testCreateCampAllowsMissingAddressStreet() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [], ['addressStreet'])]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressStreet' => null,
        ]);
    }

    public function testCreateCampAllowsNullAddressStreet() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [
            'addressStreet' => null,
        ])]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressStreet' => null,
        ]);
    }

    public function testCreateCampAllowsEmptyAddressStreet() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [
            'addressStreet' => '',
        ])]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressStreet' => '',
        ]);
    }

    public function testCreateCampValidatesLongAddressStreet() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [
            'addressStreet' => 'This camp has an extremely long address. This camp has an extremely long address. This camp has an extremely long address. This!!',
        ])]);

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

    public function testCreateCampTrimsAddressZipcode() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [
            'addressZipcode' => " 8000\t ",
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExamplePayload(Camp::class, [
            'addressZipcode' => '8000',
        ], ['periods']));
    }

    public function testCreateCampCleansHTMLFromAddressZipcode() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [
            'addressZipcode' => '800<script>alert(1)</script>0',
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExamplePayload(Camp::class, [
            'addressZipcode' => '8000',
        ], ['periods']));
    }

    public function testCreateCampAllowsMissingAddressZipcode() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [], ['addressZipcode'])]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressZipcode' => null,
        ]);
    }

    public function testCreateCampAllowsNullAddressZipcode() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [
            'addressZipcode' => null,
        ])]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressZipcode' => null,
        ]);
    }

    public function testCreateCampAllowsEmptyAddressZipcode() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [
            'addressZipcode' => '',
        ])]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressZipcode' => '',
        ]);
    }

    public function testCreateCampValidatesLongAddressZipcode() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [
            'addressZipcode' => 'This camp has an extremely long zipcode. This camp has an extremely long zipcode. This camp has an extremely long zipcode. This!!',
        ])]);

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

    public function testCreateCampTrimsAddressCity() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [
            'addressZipcode' => " Unterberg\t ",
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExamplePayload(Camp::class, [
            'addressZipcode' => 'Unterberg',
        ], ['periods']));
    }

    public function testCreateCampCleansHTMLFromAddressCity() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [
            'addressCity' => 'Unter<script>alert(1)</script>berg',
        ])]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExamplePayload(Camp::class, [
            'addressCity' => 'Unterberg',
        ], ['periods']));
    }

    public function testCreateCampAllowsMissingAddressCity() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [], ['addressCity'])]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressCity' => null,
        ]);
    }

    public function testCreateCampAllowsNullAddressCity() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [
            'addressCity' => null,
        ])]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressCity' => null,
        ]);
    }

    public function testCreateCampAllowsEmptyAddressCity() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [
            'addressCity' => '',
        ])]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'addressCity' => '',
        ]);
    }

    public function testCreateCampValidatesLongAddressCity() {
        static::createClientWithCredentials()->request('POST', '/camps', ['json' => $this->getExamplePayload(Camp::class, [
            'addressCity' => 'This camp has an extremely long city. This camp has an extremely long city. This camp has an extremely long city. This camp, I\'m telling you!',
        ])]);

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
