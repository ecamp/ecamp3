<?php

namespace eCamp\ApiTest;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\CampCollaboration;
use eCamp\Core\Entity\CampType;
use eCamp\Core\Entity\User;
use eCamp\Core\Service\CampService;
use eCamp\Core\Service\UserService;
use Zend\Http\Response;

class CampApiTest extends AbstractApiTestCase {
    function setUp() {
        parent::setUp();
        $this->createDummyData();
    }


    public function testCreateCamp1() {
        /** @var User $user */
        $user = $this->getRandomEntity(User::class);
        /** @var CampType $campType */
        $campType = $this->getRandomEntity(CampType::class);

        $this->login($user->getId());

        $this->dispatchPost(
            "/api/camp",
            [
                'owner_id' => $user->getId(),
                'camp_type_id' => $campType->getId(),
                'name' => 'name',
                'title' => 'title',
                'motto' => 'motto',
                'start' => '2018-10-11T00:00:00.000Z',
                'end' => '2018-10-14T00:00:00.000Z'
            ]
        );

        /** @var Response $resp */
        $resp = $this->getResponse();
        $body = $resp->getBody();

        $this->assertJson($body);
        $json = json_decode($body);

        /** @var CampService $campService */
        $campService = $this->getService(CampService::class);
        /** @var Camp $camp */
        $camp = $campService->fetch($json->id);

        $this->assertEquals('name', $camp->getName());
        $this->assertEquals('title', $camp->getTitle());
        $this->assertEquals('motto', $camp->getMotto());
        $this->assertCount(1, $camp->getPeriods());
    }

    public function testCreateCamp2() {
        /** @var User $user */
        $user = $this->getRandomEntity(User::class);
        /** @var CampType $campType */
        $campType = $this->getRandomEntity(CampType::class);

        $this->login($user->getId());

        $this->dispatchPost(
            "/api/camp",
            [
                'owner_id' => $user->getId(),
                'camp_type_id' => $campType->getId(),
                'name' => 'name',
                'title' => 'title',
                'motto' => 'motto',
                'periods' => [
                    [
                        'description' => 'Main',
                        'start' => '2018-10-11T00:00:00.000Z',
                        'end' => '2018-10-14T00:00:00.000Z'
                    ], [
                        'description' => 'Second',
                        'start' => '2018-10-20T00:00:00.000Z',
                        'end' => '2018-10-22T00:00:00.000Z'
                    ]
                ]
            ]
        );

        /** @var Response $resp */
        $resp = $this->getResponse();
        $body = $resp->getBody();

        $this->assertJson($body);
        $json = json_decode($body);

        /** @var CampService $campService */
        $campService = $this->getService(CampService::class);
        /** @var Camp $camp */
        $camp = $campService->fetch($json->id);

        $this->assertEquals('name', $camp->getName());
        $this->assertEquals('title', $camp->getTitle());
        $this->assertEquals('motto', $camp->getMotto());
        $this->assertCount(2, $camp->getPeriods());
    }


    public function testDeleteCamp() {
        /** @var Camp $camp */
        $camp = $this->getRandomEntity(Camp::class);
        $this->login($camp->getCreator()->getId());

        $campId = $camp->getId();
        $this->dispatchDelete("/api/camp/" . $campId);

        /** @var CampService $campService */
        $campService = $this->getService(CampService::class);
        $this->assertNull($campService->fetch($campId));
    }


    public function testCanNotDeleteCamp1() {
        $this->logout();

        /** @var Camp $camp */
        $camp = $this->getRandomEntity(Camp::class);

        $campId = $camp->getId();
        $this->dispatchDelete("/api/camp/" . $campId);

        // Camp not found
        $this->assertResponseStatusCode(Response::STATUS_CODE_404);
    }

    public function testCanNotDeleteCamp2() {
        /** @var Camp $camp */
        $camp = $this->getRandomEntity(Camp::class);

        /** @var UserService $userService */
        $userService = $this->getService(UserService::class);
        $user = $userService->create((object)[
            'username' => 'username',
            'mailAddress' => 'test@eCamp3.ch'
        ]);

        $coll = new CampCollaboration();
        $coll->setRole(CampCollaboration::ROLE_MEMBER);
        $coll->setStatus(CampCollaboration::STATUS_ESTABLISHED);
        $camp->addCampCollaboration($coll);
        $user->addCampCollaboration($coll);

        $this->getEntityManager()->persist($coll);
        $this->getEntityManager()->flush();


        $this->login($user->getId());

        $campId = $camp->getId();
        $this->dispatchDelete("/api/camp/" . $campId);

        // Camp not found
        $this->assertResponseStatusCode(Response::STATUS_CODE_403);
    }

}
