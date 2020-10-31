<?php

namespace eCamp\ApiTest;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\CampCollaboration;
use eCamp\Core\Entity\CampType;
use eCamp\Core\Entity\Organization;
use eCamp\Core\Entity\User;
use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

/**
 * @internal
 */
class CampTest extends AbstractApiControllerTestCase {
    /** @var Camp */
    protected $camp;

    /** @var User */
    protected $user;

    /** @var Organization */
    protected $organization;

    /** @var CampType */
    protected $campType;

    public function setUp() {
        parent::setUp();

        $this->user = $this->createAndAuthenticateUser();

        $this->organization = new Organization();
        $this->organization->setName('Organization');

        $this->campType = new CampType();
        $this->campType->setName('CampType');
        $this->campType->setIsJS(false);
        $this->campType->setIsCourse(false);
        $this->campType->setOrganization($this->organization);

        $this->camp = new Camp();
        $this->camp->setName('CampName');
        $this->camp->setTitle('CampTitle');
        $this->camp->setMotto('CampMotto');
        $this->camp->setCampType($this->campType);
        $this->camp->setCreator($this->user);
        $this->camp->setOwner($this->user);

        $this->getEntityManager()->persist($this->camp);
        $this->getEntityManager()->persist($this->campType);
        $this->getEntityManager()->persist($this->organization);
        $this->getEntityManager()->flush();
    }

    public function testCampFetch() {
        $this->dispatch("/api/camps/{$this->camp->getId()}", 'GET');

        $this->assertResponseStatusCode(200);

        $expectedBody = <<<JSON
            {
                "id": "{$this->camp->getId()}",
                "name": "CampName",
                "title": "CampTitle",
                "motto": "CampMotto",
                "role": "manager"
            }
JSON;

        $host = '';
        $expectedLinks = <<<JSON
            {
                "self": {
                    "href": "http://{$host}/api/camps/{$this->camp->getId()}"
                },
                "activities": {
                    "href": "http://{$host}/api/activities?campId={$this->camp->getId()}"
                }
            }
JSON;
        $expectedEmbeddedObjects = ['creator', 'campType', 'campCollaborations', 'periods', 'activityCategories'];

        $this->verifyHalResourceResponse($expectedBody, $expectedLinks, $expectedEmbeddedObjects);
    }

    public function testCampCreateWithoutName() {
        $this->setRequestContent([
            'name' => '', ]);

        $this->dispatch('/api/camps', 'POST');

        $this->assertResponseStatusCode(422);
        $this->assertObjectHasAttribute('isEmpty', $this->getResponseContent()->validation_messages->name);
        $this->assertObjectHasAttribute('isEmpty', $this->getResponseContent()->validation_messages->title);
        $this->assertObjectHasAttribute('isEmpty', $this->getResponseContent()->validation_messages->motto);
    }

    public function testCampCreateSuccess() {
        $this->setRequestContent([
            'name' => 'CampName2',
            'title' => 'CampTitle',
            'motto' => 'CampMotto',
            'campTypeId' => $this->campType->getId(), ]);

        $this->dispatch('/api/camps', 'POST');

        $this->assertResponseStatusCode(201);
        $this->assertEquals('CampName2', $this->getResponseContent()->name);
        $this->assertEquals(CampCollaboration::ROLE_MANAGER, $this->getResponseContent()->role);
    }

    public function testCampUpdateSuccess() {
        $this->setRequestContent([
            'name' => 'CampName3',
            'title' => 'CampTitle3',
            'motto' => 'CampMotto3', ]);

        $this->dispatch("/api/camps/{$this->camp->getId()}", 'PATCH');

        $this->assertResponseStatusCode(200);

        $this->assertEquals('CampName', $this->getResponseContent()->name); // camp name not changeable

        $this->assertEquals('CampTitle3', $this->getResponseContent()->title);
        $this->assertEquals('CampMotto3', $this->getResponseContent()->motto);
        $this->assertEquals('CampTitle3', $this->camp->getTitle());
    }
}
