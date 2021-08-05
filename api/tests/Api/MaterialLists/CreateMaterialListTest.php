<?php

namespace App\Tests\Api\MaterialLists;

use ApiPlatform\Core\Api\OperationType;
use App\Entity\MaterialList;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class CreateMaterialListTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator
    // TODO input filter tests
    // TODO validation tests

    public function testCreateMaterialListIsAllowedForCollaborator() {
        static::createClientWithCredentials()->request('POST', '/material_lists', ['json' => $this->getExampleWritePayload()]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains($this->getExampleReadPayload());
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

    public function testCreateMaterialListValidatesMissingName() {
        static::createClientWithCredentials()->request('POST', '/material_lists', ['json' => $this->getExampleWritePayload([], ['name'])]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                [
                    'propertyPath' => 'name',
                    'message' => 'This value should not be null.',
                ],
            ],
        ]);
    }

    public function getExampleWritePayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            MaterialList::class,
            OperationType::COLLECTION,
            'post',
            array_merge(['camp' => $this->getIriFor('camp1')], $attributes),
            [],
            $except
        );
    }

    public function getExampleReadPayload($attributes = [], $except = []) {
        return $this->getExamplePayload(
            MaterialList::class,
            OperationType::ITEM,
            'get',
            $attributes,
            ['camp'],
            $except
        );
    }
}
