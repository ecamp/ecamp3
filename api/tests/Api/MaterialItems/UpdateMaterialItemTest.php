<?php

namespace App\Tests\Api\MaterialItems;

use App\Tests\Api\ECampApiTestCase;
use PHPUnit\Framework\Attributes\TestWith;

/**
 * @internal
 */
class UpdateMaterialItemTest extends ECampApiTestCase {
    public function testPatchMaterialItemIsDeniedForAnonymousUser() {
        $materialItem = static::getFixture('materialItem1');
        static::createBasicClient()->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
            'materialList' => $this->getIriFor('materialList2WithNoItems'),
            'period' => $this->getIriFor('period1'),
            'materialNode' => null,
            'article' => 'Mehl',
            'quantity' => 1500,
            'unit' => 'g',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testPatchMaterialItemIsDeniedForUnrelatedUser() {
        $materialItem = static::getFixture('materialItem1');
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
                'materialList' => $this->getIriFor('materialList2WithNoItems'),
                'period' => $this->getIriFor('period1'),
                'materialNode' => null,
                'article' => 'Mehl',
                'quantity' => 1500,
                'unit' => 'g',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testPatchMaterialItemIsDeniedForInactiveCollaborator() {
        $materialItem = static::getFixture('materialItem1');
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
                'materialList' => $this->getIriFor('materialList2WithNoItems'),
                'period' => $this->getIriFor('period1'),
                'materialNode' => null,
                'article' => 'Mehl',
                'quantity' => 1500,
                'unit' => 'g',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testPatchMaterialItemIsDeniedForGuest() {
        $materialItem = static::getFixture('materialItem1');
        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
            ->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
                'materialList' => $this->getIriFor('materialList2WithNoItems'),
                'period' => $this->getIriFor('period1'),
                'materialNode' => null,
                'article' => 'Mehl',
                'quantity' => 1500,
                'unit' => 'g',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testPatchMaterialItemIsAllowedForMember() {
        $materialItem = static::getFixture('materialItem1');
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
                'materialList' => $this->getIriFor('materialList2WithNoItems'),
                'period' => $this->getIriFor('period1'),
                'materialNode' => null,
                'article' => 'Mehl',
                'quantity' => 1500,
                'unit' => 'g',
            ], 'headers' => ['Content-Type' => 'application/merge-patch+json']])
        ;
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'article' => 'Mehl',
            'quantity' => 1500,
            'unit' => 'g',
            '_links' => [
                'materialList' => ['href' => $this->getIriFor('materialList2WithNoItems')],
                'period' => ['href' => $this->getIriFor('period1')],
                // 'materialNode' => null,
            ],
        ]);
    }

    public function testPatchMaterialItemIsAllowedForManager() {
        $materialItem = static::getFixture('materialItem1');
        static::createClientWithCredentials()->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
            'materialList' => $this->getIriFor('materialList2WithNoItems'),
            'period' => $this->getIriFor('period1'),
            'materialNode' => null,
            'article' => 'Mehl',
            'quantity' => 1500,
            'unit' => 'g',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'article' => 'Mehl',
            'quantity' => 1500,
            'unit' => 'g',
            '_links' => [
                'materialList' => ['href' => $this->getIriFor('materialList2WithNoItems')],
                'period' => ['href' => $this->getIriFor('period1')],
                // 'materialNode' => null,
            ],
        ]);
    }

    public function testPatchMaterialItemInCampPrototypeIsDeniedForUnrelatedUser() {
        $materialItem = static::getFixture('materialItem1period1campPrototype');
        static::createClientWithCredentials()->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
            'materialNode' => null,
            'article' => 'Mehl',
            'quantity' => 1500,
            'unit' => 'g',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testPatchMaterialItemValidatesMissingMaterialList() {
        $materialItem = static::getFixture('materialItem1');
        static::createClientWithCredentials()->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
            'materialList' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'The type of the "materialList" attribute must be "array" (nested document) or "string" (IRI), "NULL" given.',
        ]);
    }

    public function testPatchMaterialItemValidatesMaterialListFromDifferentCamp() {
        $materialItem = static::getFixture('materialItem1');
        static::createClientWithCredentials()->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
            'materialList' => $this->getIriFor('materialList1camp2'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'materialList',
                    'message' => 'Must belong to the same camp.',
                ],
            ],
        ]);
    }

    public function testPatchMaterialItemAllowsPeriodInsteadOfMaterialNode() {
        $materialItem = static::getFixture('materialItem1');
        static::createClientWithCredentials()->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
            'period' => $this->getIriFor('period1'),
            'materialNode' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            '_links' => [
                'period' => ['href' => $this->getIriFor('period1')],
                // 'materialNode' => null,
            ],
        ]);
    }

    public function testPatchMaterialItemValidatesMissingPeriodAndMaterialNode() {
        $materialItem = static::getFixture('materialItem1');
        static::createClientWithCredentials()->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
            'period' => null,
            'materialNode' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'period',
                    'message' => 'Either this value or materialNode should not be null.',
                ],
                [
                    'propertyPath' => 'materialNode',
                    'message' => 'Either this value or period should not be null.',
                ],
            ],
        ]);
    }

    public function testPatchMaterialItemValidatesConflictingPeriodAndMaterialNode() {
        $materialItem = static::getFixture('materialItem1');
        static::createClientWithCredentials()->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
            'period' => $this->getIriFor('period1'),
            'materialNode' => $this->getIriFor('materialNode1'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'period',
                    'message' => 'Either this value or materialNode should be null.',
                ],
                [
                    'propertyPath' => 'materialNode',
                    'message' => 'Either this value or period should be null.',
                ],
            ],
        ]);
    }

    public function testPatchMaterialItemValidatesPeriodFromDifferentCamp() {
        $materialItem = static::getFixture('materialItem1');
        static::createClientWithCredentials()->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
            'period' => $this->getIriFor('period1camp2'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'period',
                    'message' => 'Must belong to the same camp.',
                ],
            ],
        ]);
    }

    public function testPatchMaterialItemValidatesMaterialNodeFromDifferentCamp() {
        $materialItem = static::getFixture('materialItem1');
        static::createClientWithCredentials()->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
            'materialNode' => $this->getIriFor('materialNode2'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'materialNode',
                    'message' => 'Must belong to the same camp.',
                ],
            ],
        ]);
    }

    public function testPatchMaterialItemValidatesMissingArticle() {
        $materialItem = static::getFixture('materialItem1');
        static::createClientWithCredentials()->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
            'article' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'The type of the "article" attribute must be "string", "NULL" given.',
        ]);
    }

    public function testPatchMaterialItemValidatesArticleMinLength() {
        $materialItem = static::getFixture('materialItem1');
        static::createClientWithCredentials()->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
            'article' => '',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'article',
                    'message' => 'This value should not be blank.',
                ],
            ],
        ]);
    }

    public function testPatchMaterialItemValidatesArticleMaxLength() {
        $materialItem = static::getFixture('materialItem1');
        static::createClientWithCredentials()->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
            'article' => str_repeat('a', 65),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'article',
                    'message' => 'This value is too long. It should have 64 characters or less.',
                ],
            ],
        ]);
    }

    public function testPatchMaterialItemValidatesTrimsArticle() {
        $materialItem = static::getFixture('materialItem1');
        static::createClientWithCredentials()->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
            'article' => " \tarticle\t ",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'article' => 'article',
        ]);
    }

    public function testPatchMaterialItemValidatesCleansTextOnArticle() {
        $materialItem = static::getFixture('materialItem1');
        static::createClientWithCredentials()->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
            'article' => "\u{000A}article\u{0007}",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'article' => 'article',
        ]);
    }

    public function testPatchMaterialItemAllowsMissingQuantity() {
        $materialItem = static::getFixture('materialItem1');
        static::createClientWithCredentials()->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
            'quantity' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['quantity' => null]);
    }

    public function testPatchMaterialItemValidatesInvalidQuantity() {
        $materialItem = static::getFixture('materialItem1');
        static::createClientWithCredentials()->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
            'quantity' => '1',
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'detail' => 'The type of the "quantity" attribute must be "float", "string" given.',
        ]);
    }

    #[TestWith([0])]
    #[TestWith([-0])]
    #[TestWith([-0.1])]
    #[TestWith([-1])]
    public function testPatchMaterialItemRejectsNegativeQuantity(float $quantity) {
        $materialItem = static::getFixture('materialItem1');
        static::createClientWithCredentials()->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
            'quantity' => $quantity,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'detail' => 'quantity: This value should be greater than 0.',
        ]);
    }

    public function testPatchMaterialItemAcceptsLargeNumberForQuantity() {
        $materialItem = static::getFixture('materialItem1');
        static::createClientWithCredentials()->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
            // around PHP_FLOAT_MAX. We cannot send a greater number with php.
            // Via the Swagger UI values greater than FLOAT_MAX result in 0.
            'quantity' => 1.7E308,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'quantity' => 1.7E308,
        ]);
    }

    public function testPatchMaterialItemDoesNotCrashForLargeNumberForQuantity() {
        $materialItem = static::getFixture('materialItem1');
        static::createClientWithCredentials()->request(
            'PATCH',
            '/material_items/'.$materialItem->getId(),
            [
                'body' => <<<'EOF'
                        {
                           "quantity": 1e500
                         }
                        EOF,
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json',
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'quantity' => 0,
        ]);
    }

    public function testPatchMaterialItemAllowsMissingUnit() {
        $materialItem = static::getFixture('materialItem1');
        static::createClientWithCredentials()->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
            'unit' => null,
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['unit' => null]);
    }

    public function testPatchMaterialItemValidatesUnitMaxLength() {
        $materialItem = static::getFixture('materialItem1');
        static::createClientWithCredentials()->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
            'unit' => str_repeat('a', 33),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'unit',
                    'message' => 'This value is too long. It should have 32 characters or less.',
                ],
            ],
        ]);
    }

    public function testPatchMaterialItemValidatesTrimsUnit() {
        $materialItem = static::getFixture('materialItem1');
        static::createClientWithCredentials()->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
            'unit' => " \tunit\t ",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'unit' => 'unit',
        ]);
    }

    public function testPatchMaterialItemValidatesCleansTextOnUnit() {
        $materialItem = static::getFixture('materialItem1');
        static::createClientWithCredentials()->request('PATCH', '/material_items/'.$materialItem->getId(), ['json' => [
            'unit' => "\u{000A}unit\u{0007}",
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'unit' => 'unit',
        ]);
    }
}
