<?php

namespace App\Tests\Api\Checklists;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Entity\Checklist;
use App\Tests\Api\ECampApiTestCase;
use App\Tests\Constraints\CompatibleHalResponse;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

use function PHPUnit\Framework\assertThat;

/**
 * @internal
 */
class CreateChecklistTest extends ECampApiTestCase {
    // Prototype-Checklist

    public function testCreatePrototypeChecklistIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('POST', '/checklists', ['json' => $this->getExampleWritePayload(['isPrototype' => true, 'camp' => null])]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testCreatePrototypeChecklistIsDeniedForUser() {
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('POST', '/checklists', ['json' => $this->getExampleWritePayload(['isPrototype' => true, 'camp' => null])])
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testCreatePrototypeChecklistIsAllowedForAdmin() {
        static::createClientWithCredentials(['email' => static::$fixtures['admin']->getEmail()])
            ->request('POST', '/checklists', ['json' => $this->getExampleWritePayload(['isPrototype' => true, 'camp' => null])])
        ;

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload(['isPrototype' => true]));
    }

    public function testCreatePrototypeChecklistWithCampIsDenied() {
        static::createClientWithCredentials(['email' => static::$fixtures['admin']->getEmail()])
            ->request('POST', '/checklists', ['json' => $this->getExampleWritePayload(['isPrototype' => true, 'camp' => $this->getIriFor('campPrototype')])])
        ;

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'camp',
                    'message' => 'This value should be null.',
                ],
            ],
        ]);
    }

    // Camp-Checklist

    public function testCreateChecklistIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('POST', '/checklists', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testCreateChecklistIsNotPossibleForUnrelatedUserBecauseCampIsNotReadable() {
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('POST', '/checklists', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('camp1').'".',
        ]);
    }

    public function testCreateChecklistIsNotPossibleForInactiveCollaboratorBecauseCampIsNotReadable() {
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('POST', '/checklists', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('camp1').'".',
        ]);
    }

    public function testCreateChecklistIsDeniedForGuest() {
        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
            ->request('POST', '/checklists', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testCreateChecklistIsAllowedForMember() {
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('POST', '/checklists', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload());
    }

    public function testCreateChecklistIsAllowedForManager() {
        static::createClientWithCredentials()->request('POST', '/checklists', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload());
    }

    public function testCreateChecklistInCampPrototypeIsDeniedForUnrelatedUser() {
        static::createClientWithCredentials()->request('POST', '/checklists', ['json' => $this->getExampleWritePayload([
            'camp' => $this->getIriFor('campPrototype'),
        ])]);

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testCreateChecklistValidatesMissingCamp() {
        static::createClientWithCredentials()->request('POST', '/checklists', ['json' => $this->getExampleWritePayload([], ['camp'])]);

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testCreateChecklistValidatesMissingName() {
        static::createClientWithCredentials()->request('POST', '/checklists', ['json' => $this->getExampleWritePayload([], ['name'])]);

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

    public function testCreateChecklistValidatesBlankName() {
        static::createClientWithCredentials()->request(
            'POST',
            '/checklists',
            [
                'json' => $this->getExampleWritePayload(
                    [
                        'name' => '',
                    ]
                ),
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

    public function testCreateChecklistValidatesTooLongName() {
        static::createClientWithCredentials()->request(
            'POST',
            '/checklists',
            [
                'json' => $this->getExampleWritePayload(
                    [
                        'name' => str_repeat('l', 33),
                    ]
                ),
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

    public function testCreateChecklistTrimsName() {
        static::createClientWithCredentials()->request(
            'POST',
            '/checklists',
            [
                'json' => $this->getExampleWritePayload(
                    [
                        'name' => "  \t Ausbildungsziele\t ",
                    ]
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload(
            [
                'name' => 'Ausbildungsziele',
            ]
        ));
    }

    public function testCreateChecklistCleansForbiddenCharactersFromName() {
        static::createClientWithCredentials()->request(
            'POST',
            '/checklists',
            [
                'json' => $this->getExampleWritePayload(
                    [
                        'name' => "\n\t<b>Ausbildungsziele",
                    ]
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload(
            [
                'name' => '<b>Ausbildungsziele',
            ]
        ));
    }

    public function testCreateChecklistFromCopySourceValidatesAccess() {
        static::createClientWithCredentials(['email' => static::$fixtures['user8memberOnlyInCamp2']->getEmail()])->request(
            'POST',
            '/checklists',
            ['json' => $this->getExampleWritePayload(
                [
                    'camp' => $this->getIriFor('camp2'),
                    'copyChecklistSource' => $this->getIriFor('checklist1'),
                ]
            )]
        );

        // No Access on checklist1 -> BadRequest
        $this->assertResponseStatusCodeSame(400);
    }

    public function testCreateChecklistFromCopySourceWithinSameCamp() {
        static::createClientWithCredentials()->request(
            'POST',
            '/checklists',
            ['json' => $this->getExampleWritePayload(
                [
                    'camp' => $this->getIriFor('camp1'),
                    'copyChecklistSource' => $this->getIriFor('checklist1'),
                ],
            )]
        );

        // Checklist created
        $this->assertResponseStatusCodeSame(201);
    }

    public function testCreateChecklistFromCopySourceAcrossCamp() {
        static::createClientWithCredentials()->request(
            'POST',
            '/checklists',
            ['json' => $this->getExampleWritePayload(
                [
                    'camp' => $this->getIriFor('camp2'),
                    'copyChecklistSource' => $this->getIriFor('checklist1'),
                ],
            )]
        );

        // Checklist created
        $this->assertResponseStatusCodeSame(201);
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testCreateResponseStructureMatchesReadResponseStructure() {
        $client = static::createClientWithCredentials();
        $client->disableReboot();
        $createResponse = $client->request(
            'POST',
            '/checklists',
            [
                'json' => $this->getExampleWritePayload(),
            ]
        );

        $this->assertResponseStatusCodeSame(201);

        $createArray = $createResponse->toArray();
        $newItemLink = $createArray['_links']['self']['href'];
        $getItemResponse = $client->request('GET', $newItemLink);

        assertThat($createArray, CompatibleHalResponse::isHalCompatibleWith($getItemResponse->toArray()));
    }

    public function getExampleWritePayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            Checklist::class,
            Post::class,
            array_merge([
                'isPrototype' => false,
                'copyChecklistSource' => null,
                'camp' => $this->getIriFor('camp1'),
            ], $attributes),
            [],
            $except
        );
    }

    public function getExampleReadPayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            Checklist::class,
            Get::class,
            array_merge([
                'isPrototype' => false,
            ], $attributes),
            ['camp', 'preferredContentTypes'],
            $except
        );
    }
}
