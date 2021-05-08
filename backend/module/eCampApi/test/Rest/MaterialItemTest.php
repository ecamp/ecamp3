<?php

namespace eCamp\ApiTest\Rest;

use Doctrine\Common\DataFixtures\Loader;
use eCamp\Core\Entity\ContentNode;
use eCamp\Core\Entity\MaterialItem;
use eCamp\Core\Entity\MaterialList;
use eCamp\Core\Entity\Period;
use eCamp\CoreTest\Data\MaterialItemTestData;
use eCamp\CoreTest\Data\PeriodTestData;
use eCamp\CoreTest\Data\UserTestData;
use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

/**
 * @internal
 */
class MaterialItemTest extends AbstractApiControllerTestCase {
    /** @var Period */
    protected $period;

    /** @var ContentNode */
    protected $contentNode;

    /** @var MaterialList */
    protected $materialList;

    /** @var MaterialItem */
    protected $materialItem;

    /** @var User */
    protected $user;

    private $apiEndpoint = '/api/material-items';

    public function setUp(): void {
        parent::setUp();

        $userLoader = new UserTestData();
        $periodLoader = new PeriodTestData();
        $materialItemLoader = new MaterialItemTestData($this->getApplication()->getServiceManager());

        $loader = new Loader();
        $loader->addFixture($userLoader);
        $loader->addFixture($periodLoader);
        $loader->addFixture($materialItemLoader);
        $this->loadFixtures($loader);

        $this->user = $userLoader->getReference(UserTestData::$USER1);
        $this->period = $periodLoader->getReference(PeriodTestData::$PERIOD1);
        $this->contentNode = $materialItemLoader->getReference(MaterialItemTestData::$CONTENTNODE1);
        $this->materialItem = $materialItemLoader->getReference(MaterialItemTestData::$MATERIALITEM1);
        $this->materialList = $this->materialItem->getMaterialList();

        $this->authenticateUser($this->user);
    }

    public function testFetch(): void {
        $this->dispatch("{$this->apiEndpoint}/{$this->materialItem->getId()}", 'GET');

        $this->assertResponseStatusCode(200);

        $expectedBody = <<<JSON
            {
                "id": "{$this->materialItem->getId()}",
                "quantity": 2,
                "unit": "kg",
                "article": "art",
                "period": null
            }
JSON;

        $expectedLinks = <<<JSON
            {
                "self": {
                    "href": "http://{$this->host}{$this->apiEndpoint}/{$this->materialItem->getId()}"
                }
            }
JSON;
        $expectedEmbeddedObjects = ['materialList', 'contentNode'];

        $this->verifyHalResourceResponse($expectedBody, $expectedLinks, $expectedEmbeddedObjects);
    }

    public function testCreatePeriodItemSuccess(): void {
        $this->setRequestContent([
            'quantity' => 2,
            'unit' => 'kg',
            'article' => 'water',
            'materialListId' => $this->materialList->getId(),
            'periodId' => $this->period->getId(),
        ]);

        $this->dispatch($this->apiEndpoint, 'POST');

        $this->assertResponseStatusCode(201);
        $this->assertEquals(2, $this->getResponseContent()->quantity);
        $this->assertEquals('kg', $this->getResponseContent()->unit);
        $this->assertEquals('water', $this->getResponseContent()->article);
    }

    public function testCreateActivityItemSuccess(): void {
        $this->setRequestContent([
            'quantity' => 2,
            'unit' => 'kg',
            'article' => 'water',
            'materialListId' => $this->materialList->getId(),
            'contentNodeId' => $this->contentNode->getId(),
        ]);

        $this->dispatch($this->apiEndpoint, 'POST');

        $this->assertResponseStatusCode(201);
        $this->assertEquals(2, $this->getResponseContent()->quantity);
        $this->assertEquals('kg', $this->getResponseContent()->unit);
        $this->assertEquals('water', $this->getResponseContent()->article);
    }

    public function testUpdateSuccess(): void {
        $this->setRequestContent([
            'quantity' => 10,
            'unit' => 'kg',
            'article' => 'water',
        ]);

        $this->dispatch($this->apiEndpoint.'/'.$this->materialItem->getId(), 'PATCH');

        $this->assertResponseStatusCode(200);
        $this->assertEquals(10, $this->getResponseContent()->quantity);
        $this->assertEquals('kg', $this->getResponseContent()->unit);
        $this->assertEquals('water', $this->getResponseContent()->article);
    }

    public function testDeleteSuccess(): void {
        $this->dispatch($this->apiEndpoint.'/'.$this->materialItem->getId(), 'DELETE');

        $this->assertResponseStatusCode(204);

        $result = $this->getEntityManager()->find(MaterialItem::class, $this->materialItem->getId());
        $this->assertNull($result);
    }

    /**
     * Special cases.
     */
    public function testCreateWithValidQueryParams(): void {
        $this->setRequestContent([
            'article' => 'water',
            'materialListId' => $this->materialList->getId(),
            'periodId' => $this->period->getId(),
        ]);

        $this->dispatch("{$this->apiEndpoint}?quantity=2", 'POST');

        $this->assertResponseStatusCode(201);
        $this->assertEquals(2, $this->getResponseContent()->quantity);
    }

    public function testCreateWithInvalidQueryParams(): void {
        $this->setRequestContent([
            'article' => 'water',
            'materialListId' => $this->materialList->getId(),
            'periodId' => $this->period->getId(),
        ]);

        $this->dispatch("{$this->apiEndpoint}?quantity=two", 'POST');

        $this->assertResponseStatusCode(422);
    }
}
