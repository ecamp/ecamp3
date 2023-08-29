<?php

namespace App\Tests\Api\MaterialLists;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Entity\MaterialList;
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
class CreateMaterialListTest extends ECampApiTestCase {
    public function testCreateMaterialListIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('POST', '/material_lists', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testCreateMaterialListIsNotPossibleForUnrelatedUserBecauseCampIsNotReadable() {
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('POST', '/material_lists', ['json' => $this->getExampleWritePayload()])
        ;
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('camp1').'".',
        ]);
    }

    public function testCreateMaterialListIsNotPossibleForInactiveCollaboratorBecauseCampIsNotReadable() {
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('POST', '/material_lists', ['json' => $this->getExampleWritePayload()])
        ;
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('camp1').'".',
        ]);
    }

    public function testCreateMaterialListIsDeniedForGuest() {
        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
            ->request('POST', '/material_lists', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testCreateMaterialListIsAllowedForMember() {
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('POST', '/material_lists', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload());
    }

    public function testCreateMaterialListIsAllowedForManager() {
        static::createClientWithCredentials()->request('POST', '/material_lists', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload());
    }

    public function testCreateMaterialListInCampPrototypeIsDeniedForUnrelatedUser() {
        static::createClientWithCredentials()->request('POST', '/material_lists', ['json' => $this->getExampleWritePayload([
            'camp' => $this->getIriFor('campPrototype'),
        ])]);

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testCreateMaterialListValidatesMissingCamp() {
        static::createClientWithCredentials()->request('POST', '/material_lists', ['json' => $this->getExampleWritePayload([], ['camp'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'camp',
                    'message' => 'This value should not be null.',
                ],
            ],
        ]);
    }

    public function testCreateMaterialListDisallowsSettingCampCollaboration() {
        static::createClientWithCredentials()
            ->request(
                'POST',
                '/material_lists',
                [
                    'json' => $this->getExampleWritePayload([
                        'campCollaboration' => $this->getIriFor('campCollaboration1manager'),
                    ]),
                ]
            )
        ;

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'Extra attributes are not allowed ("campCollaboration" is unknown).',
        ]);
    }

    public function testCreateMaterialListValidatesMissingName() {
        static::createClientWithCredentials()->request('POST', '/material_lists', ['json' => $this->getExampleWritePayload([], ['name'])]);

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

    public function testCreateMaterialListValidatesTooLongName() {
        static::createClientWithCredentials()
            ->request(
                'POST',
                '/material_lists',
                [
                    'json' => $this->getExampleWritePayload([
                        'name' => 'a very long name with more than a',
                    ]),
                ]
            )
        ;

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

    public function testCreateMaterialListTrimsName() {
        static::createClientWithCredentials()
            ->request(
                'POST',
                '/material_lists',
                [
                    'json' => $this->getExampleWritePayload([
                        'name' => ' Something ',
                    ]),
                ]
            )
        ;

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'name' => 'Something',
        ]);
    }

    public function testCreateMaterialListCleansForbiddenCharactersFromName() {
        static::createClientWithCredentials()
            ->request(
                'POST',
                '/material_lists',
                [
                    'json' => $this->getExampleWritePayload([
                        'name' => " \n\t<b>t</b ",
                    ]),
                ]
            )
        ;

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'name' => '<b>t</b',
        ]);
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
            '/material_lists',
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
            MaterialList::class,
            Post::class,
            array_merge(['camp' => $this->getIriFor('camp1')], $attributes),
            [],
            $except
        );
    }

    public function getExampleReadPayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            MaterialList::class,
            Get::class,
            $attributes,
            ['camp'],
            $except
        );
    }
}
