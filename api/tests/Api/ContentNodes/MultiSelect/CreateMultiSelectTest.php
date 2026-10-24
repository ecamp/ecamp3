<?php

namespace App\Tests\Api\ContentNodes\MultiSelect;

use App\Entity\ContentNode\MultiSelect;
use App\Tests\Api\ContentNodes\CreateContentNodeTestCase;

/**
 * @internal
 */
class CreateMultiSelectTest extends CreateContentNodeTestCase {
    #[\Override]
    public function setUp(): void {
        parent::setUp();

        $this->endpoint = '/content_node/multi_selects';
        $this->entityClass = MultiSelect::class;
        $this->defaultContentType = static::getFixture('contentTypeMultiSelect');
    }

    public function testCreateMultiSelectCopiesOptionsFromContentType() {
        // when
        $this->create($this->getExampleWritePayload());

        // then
        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'data' => [
                'options' => [
                    'outdoorTechnique' => ['checked' => false],
                    'security' => ['checked' => false],
                    'natureAndEnvironment' => ['checked' => false],
                    'pioneeringTechnique' => ['checked' => false],
                    'campsiteAndSurroundings' => ['checked' => false],
                    'preventionAndIntegration' => ['checked' => false],
                ],
            ],
        ]);
    }

    public function testYSActivityCharacteristicsCanBeSelectedChangedAndCleared() {
        $this->defaultContentType = static::getFixture('contentTypeYSActivityCharacteristics');
        $client = static::createClientWithCredentials();
        $client->disableReboot();
        $response = $client->request('POST', $this->endpoint, ['json' => $this->getExampleWritePayload()]);
        $this->assertResponseStatusCodeSame(201);
        $created = $response->toArray();
        $options = array_fill_keys([
            'outdoorTechnique', 'travellingSafety', 'gamesAndSports',
            'gettingInvolved', 'community', 'natureAndEnvironment',
        ], ['checked' => false]);
        ksort($options);
        $createdOptions = $created['data']['options'];
        ksort($createdOptions);
        $this->assertSame($options, $createdOptions);
        $this->assertSame('YSActivityCharacteristics', $created['contentTypeName']);
        $iri = $created['_links']['self']['href'];

        foreach ([
            ['outdoorTechnique', 'community'],
            ['travellingSafety', 'gamesAndSports', 'gettingInvolved', 'natureAndEnvironment'],
            [],
        ] as $selected) {
            foreach ($options as $key => &$option) {
                $option['checked'] = in_array($key, $selected, true);
            }
            $client->request('PATCH', $iri, ['json' => ['data' => ['options' => $options]], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);
            $this->assertResponseStatusCodeSame(200);
            $reloaded = $client->request('GET', $iri);
            $this->assertResponseStatusCodeSame(200);
            $reloadedOptions = $reloaded->toArray()['data']['options'];
            ksort($reloadedOptions);
            $this->assertSame($options, $reloadedOptions);
        }
    }

    public function testCreateDoesNotAcceptOptions() {
        $this->create($this->getExampleWritePayload([
            'data' => [
                'options' => [],
            ],
        ]));

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            'violations' => [
                0 => [
                    'propertyPath' => 'data',
                    'message' => 'This value should be null.',
                ],
            ],
        ]);
    }

    #[\Override]
    protected function getExampleWritePayload($attributes = [], $except = []) {
        return parent::getExampleWritePayload(
            array_merge([
                'data' => null,
            ], $attributes),
            $except
        );
    }
}
