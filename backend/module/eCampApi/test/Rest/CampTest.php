<?php

namespace eCamp\ApiTest\Rest;

use Doctrine\Common\DataFixtures\Loader;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\CampCollaboration;
use eCamp\Core\Entity\User;
use eCamp\CoreTest\Data\CampTestData;
use eCamp\CoreTest\Data\UserTestData;
use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

/**
 * @internal
 */
class CampTest extends AbstractApiControllerTestCase {
    /** @var Camp */
    protected $camp;

    /** @var User */
    protected $user;

    public function setUp(): void {
        parent::setUp();

        $userLoader = new UserTestData();
        $campLoader = new CampTestData();

        $loader = new Loader();
        $loader->addFixture($userLoader);
        $loader->addFixture($campLoader);
        $this->loadFixtures($loader);

        $this->user = $userLoader->getReference(UserTestData::$USER1);
        $this->camp = $campLoader->getReference(CampTestData::$CAMP1);

        $this->authenticateUser($this->user);
    }

    public function testFetch(): void {
        $this->dispatch("/api/camps/{$this->camp->getId()}", 'GET');

        $this->assertResponseStatusCode(200);

        $expectedBody = <<<JSON
            {
                "id": "{$this->camp->getId()}",
                "name": "CampName",
                "title": "CampTitle",
                "motto": "CampMotto",
                "addressName": "AdrName",
                "addressStreet": "AdrStreet",
                "addressZipcode": "AdrZipcode",
                "addressCity": "AdrCity",
                "role": "manager",
                "isPrototype": false
            }
JSON;

        $expectedLinks = <<<JSON
            {
                "self": {
                    "href": "http://{$this->host}/api/camps/{$this->camp->getId()}"
                },
                "activities": {
                    "href": "http://{$this->host}/api/activities?campId={$this->camp->getId()}"
                },
                "materialLists": {
                    "href": "http://{$this->host}/api/material-lists?campId={$this->camp->getId()}"
                }
            }
JSON;
        $expectedEmbeddedObjects = ['creator', 'campCollaborations', 'periods', 'categories'];

        $this->verifyHalResourceResponse($expectedBody, $expectedLinks, $expectedEmbeddedObjects);
    }

    public function testFetchAll(): void {
        $this->dispatch('/api/camps?page_size=10', 'GET');

        $this->assertResponseStatusCode(200);

        $this->assertEquals(2, $this->getResponseContent()->total_items);
        $this->assertEquals(10, $this->getResponseContent()->page_size);
        $this->assertEquals("http://{$this->host}/api/camps?page_size=10&page=1", $this->getResponseContent()->_links->self->href);
    }

    public function testCreateWithoutName(): void {
        $this->setRequestContent([
            'name' => '', ]);

        $this->dispatch('/api/camps', 'POST');

        $this->assertResponseStatusCode(422);
        $this->assertObjectHasAttribute('isEmpty', $this->getResponseContent()->validation_messages->name);
        $this->assertObjectHasAttribute('isEmpty', $this->getResponseContent()->validation_messages->title);
    }

    public function testCreateSuccess(): void {
        $this->setRequestContent([
            'name' => 'CampName2',
            'title' => 'CampTitle',
            'motto' => 'CampMotto', // TODO for discussion: Should motto really be mandatory?
        ]);

        $this->dispatch('/api/camps', 'POST');

        $this->assertResponseStatusCode(201);
        $this->assertEquals('CampName2', $this->getResponseContent()->name);
        $this->assertEquals(CampCollaboration::ROLE_MANAGER, $this->getResponseContent()->role);
    }

    public function testUpdateSuccess(): void {
        $this->setRequestContent([
            'name' => 'CampName3',
            'title' => 'CampTitle3',
            'motto' => 'CampMotto3', ]);

        $this->dispatch("/api/camps/{$this->camp->getId()}", 'PATCH');

        $this->assertResponseStatusCode(200);

        $this->assertEquals('CampName3', $this->getResponseContent()->name);
        $this->assertEquals('CampTitle3', $this->getResponseContent()->title);
        $this->assertEquals('CampMotto3', $this->getResponseContent()->motto);
        $this->assertEquals('CampTitle3', $this->camp->getTitle());
    }

    public function testDelete(): void {
        $this->dispatch("/api/camps/{$this->camp->getId()}", 'DELETE');

        $this->assertResponseStatusCode(204);

        $result = $this->getEntityManager()->find(Camp::class, $this->camp->getId());
        $this->assertNull($result);
    }
}
