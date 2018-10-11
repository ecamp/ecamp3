<?php

namespace eCamp\ApiTest;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\CampType;
use eCamp\Core\Entity\User;
use eCamp\Core\Service\CampService;
use eCamp\LibTest\PHPUnit\AbstractHttpControllerTestCase;
use Zend\Stdlib\Parameters;

class CampApiTest extends AbstractHttpControllerTestCase {
    function setUp() {
        parent::setUp();
        $this->createDummyData();
    }


    public function testCreateCamp() {
        /** @var User $user */
        $user = $this->getRandomEntity(User::class);
        /** @var CampType $campType */
        $campType = $this->getRandomEntity(CampType::class);

        $this->login($user->getId());

        /** @var CampService $campService */
        $campService = $this->getService(CampService::class);
        $campCountOld = $campService->fetchAll()->getTotalItemCount();

        /** @var \Zend\Http\PhpEnvironment\Request $request */
        $request = $this->getRequest();
        $request->setMethod('POST');
        $request->getHeaders()->addHeaderLine('Accept', 'application/json');
        $request->setPost(new Parameters([
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
        ]));
        $this->dispatch("/api/camp");

        $campCountNew = $campService->fetchAll()->getTotalItemCount();

        $this->assertEquals($campCountOld + 1, $campCountNew);
    }

    public function testDeleteCamp() {
        /** @var Camp $camp */
        $camp = $this->getRandomEntity(Camp::class);
        $this->login($camp->getCreator()->getId());

        $campId = $camp->getId();

        /** @var \Zend\Http\PhpEnvironment\Request $request */
        $request = $this->getRequest();
        $request->setMethod('DELETE');
        $request->getHeaders()->addHeaderLine('Accept', 'application/json');
        $this->dispatch("/api/camp/" . $campId);

        /** @var CampService $campService */
        $campService = $this->getService(CampService::class);
        $this->assertNull($campService->fetch($campId));
    }

}
