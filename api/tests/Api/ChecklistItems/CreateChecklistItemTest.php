<?php

namespace App\Tests\Api\ChecklistItems;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Entity\ChecklistItem;
use App\Tests\Api\ECampApiTestCase;
use App\Tests\Constraints\CompatibleHalResponse;

use function PHPUnit\Framework\assertThat;

/**
 * @internal
 */
class CreateChecklistItemTest extends ECampApiTestCase {
    public function testCreateChecklistItemIsDeniedForAnonymousUser() {
        static::createBasicClient()->request('POST', '/checklist_items', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testCreateChecklistItemIsNotPossibleForUnrelatedUserBecauseCampIsNotReadable() {
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('POST', '/checklist_items', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('checklist1').'".',
        ]);
    }

    public function testCreateChecklistItemIsNotPossibleForInactiveCollaboratorBecauseCampIsNotReadable() {
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('POST', '/checklist_items', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Item not found for "'.$this->getIriFor('checklist1').'".',
        ]);
    }

    public function testCreateChecklistItemIsDeniedForGuest() {
        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
            ->request('POST', '/checklist_items', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testCreateChecklistItemIsAllowedForMember() {
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('POST', '/checklist_items', ['json' => $this->getExampleWritePayload()])
        ;

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload(['position' => 2]));
    }

    public function testCreateChecklistItemIsAllowedForManager() {
        static::createClientWithCredentials()->request('POST', '/checklist_items', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload(['position' => 2]));
    }

    public function testCreateChecklistItemInCampPrototypeIsDeniedForUnrelatedUser() {
        static::createClientWithCredentials()->request('POST', '/checklist_items', ['json' => $this->getExampleWritePayload([
            'checklist' => $this->getIriFor('checklist1campPrototype'),
        ])]);

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testCreateChecklistItemValidatesMissingChecklist() {
        static::createClientWithCredentials()->request('POST', '/checklist_items', ['json' => $this->getExampleWritePayload([], ['checklist'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'checklist',
                    'message' => 'This value should not be null.',
                ],
            ],
        ]);
    }

    public function testCreateChecklistItemValidatesMissingText() {
        static::createClientWithCredentials()->request('POST', '/checklist_items', ['json' => $this->getExampleWritePayload([], ['text'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'text',
                    'message' => 'This value should not be blank.',
                ],
            ],
        ]);
    }

    public function testCreateChecklistItemValidatesBlankText() {
        static::createClientWithCredentials()->request(
            'POST',
            '/checklist_items',
            [
                'json' => $this->getExampleWritePayload(
                    [
                        'text' => '',
                    ]
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'text',
                    'message' => 'This value should not be blank.',
                ],
            ],
        ]);
    }

    public function testCreateChecklistItemValidatesTooLongText() {
        static::createClientWithCredentials()->request(
            'POST',
            '/checklist_items',
            [
                'json' => $this->getExampleWritePayload(
                    [
                        'text' => str_repeat('l', 257),
                    ]
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'text',
                    'message' => 'This value is too long. It should have 256 characters or less.',
                ],
            ],
        ]);
    }

    public function testCreateChecklistItemTrimsText() {
        static::createClientWithCredentials()->request(
            'POST',
            '/checklist_items',
            [
                'json' => $this->getExampleWritePayload(
                    [
                        'text' => "  \t Ziel 1\t ",
                    ]
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload(
            [
                'text' => 'Ziel 1',
                'position' => 2,
            ]
        ));
    }

    public function testCreateChecklistItemCleansForbiddenCharactersFromText() {
        static::createClientWithCredentials()->request(
            'POST',
            '/checklist_items',
            [
                'json' => $this->getExampleWritePayload(
                    [
                        'text' => "\n\t<b>Ziel 1",
                    ]
                ),
            ]
        );

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload(
            [
                'text' => '<b>Ziel 1',
                'position' => 2,
            ]
        ));
    }

    public function testCreateChecklistItemIsDeniedForTooDeepNesting() {
        $checklistItem = static::getFixture('checklistItem1_1_2_3_4');

        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request(
                'POST',
                '/checklist_items',
                [
                    'json' => $this->getExampleWritePayload([
                        'parent' => '/checklist_items/'.$checklistItem->getId(),
                    ]),
                ]
            )
        ;

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'parent: Nesting can be a maximum of 3 levels deep.',
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
            '/checklist_items',
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
            ChecklistItem::class,
            Post::class,
            array_merge([
                'parent' => null,
                'checklist' => $this->getIriFor('checklist1'),
            ], $attributes),
            [],
            $except
        );
    }

    public function getExampleReadPayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            ChecklistItem::class,
            Get::class,
            $attributes,
            ['parent', 'checklist'],
            $except
        );
    }
}
