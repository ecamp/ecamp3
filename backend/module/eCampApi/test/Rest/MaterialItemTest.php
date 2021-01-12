<?php

namespace eCamp\ApiTest\Rest;

use Doctrine\Common\DataFixtures\Loader;
use eCamp\Core\Entity\ActivityContent;
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

    /** @var ActivityContent */
    protected $activityContent;

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
        $this->activityContent = $materialItemLoader->getReference(MaterialItemTestData::$ACTIVITYCONTENT1);
        $this->materialItem = $materialItemLoader->getReference(MaterialItemTestData::$MATERIALITEM1);
        $this->materialList = $this->materialItem->getMaterialList();

        $this->authenticateUser($this->user);
    }

    public function testFetch() {
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
        $expectedEmbeddedObjects = ['materialList', 'activityContent'];

        $this->verifyHalResourceResponse($expectedBody, $expectedLinks, $expectedEmbeddedObjects);
    }

    public function testCreatePeriodItemSuccess() {
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

    public function testCreateActivityItemSuccess() {
        $this->setRequestContent([
            'quantity' => 2,
            'unit' => 'kg',
            'article' => 'water',
            'materialListId' => $this->materialList->getId(),
            'activityContentId' => $this->activityContent->getId(),
        ]);

        $this->dispatch($this->apiEndpoint, 'POST');

        $this->assertResponseStatusCode(201);
        $this->assertEquals(2, $this->getResponseContent()->quantity);
        $this->assertEquals('kg', $this->getResponseContent()->unit);
        $this->assertEquals('water', $this->getResponseContent()->article);
    }

    public function testUpdateSuccess() {
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

    public function testDeleteSuccess() {
        $this->dispatch($this->apiEndpoint.'/'.$this->materialItem->getId(), 'DELETE');

        $this->assertResponseStatusCode(204);

        $result = $this->getEntityManager()->find(MaterialItem::class, $this->materialItem->getId());
        $this->assertNull($result);
    }
}
