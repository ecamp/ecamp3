<?php

namespace eCamp\ApiTest;

use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\CampType;
use eCamp\Core\Entity\Organization;
use eCamp\Core\Entity\User;
use eCamp\Core\EntityService\UserService;
use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;
use Laminas\Authentication\AuthenticationService;

/**
 * @internal
 */
class CampTest extends AbstractApiControllerTestCase {
    public function testCampFetch() {
        $user = $this->createAndAuthenticateUser();

        $organization = new Organization();
        $organization->setName('Organization');

        $campType = new CampType();
        $campType->setName('CampType');
        $campType->setIsJS(false);
        $campType->setIsCourse(false);
        $campType->setOrganization($organization);

        $camp = new Camp();
        $camp->setName('CampName');
        $camp->setTitle('CampTitle');
        $camp->setMotto('CampMotto');
        $camp->setCampType($campType);
        $camp->setCreator($user);
        $camp->setOwner($user);

        $this->getEntityManager()->persist($camp);
        $this->getEntityManager()->persist($campType);
        $this->getEntityManager()->persist($organization);
        $this->getEntityManager()->flush();

        $this->dispatch("/api/camps/{$camp->getId()}", 'GET');

        $this->assertResponseStatusCode(200);

        $host = '';
        $expectedResponse = <<<JSON
            {
                "id": "{$camp->getId()}",
                "name": "CampName",
                "title": "CampTitle",
                "motto": "CampMotto",
                "role": "manager",
                "_embedded": {
                    "creator": {
                        "_links": {
                            "self": {
                                "href": "http://{$host}/api/users/{$user->getId()}"
                            }
                        }
                    },
                    "campType": {
                        "id": "{$campType->getId()}",
                        "name": "CampType",
                        "isJS": false,
                        "isCourse": false,
                        "_embedded": {
                            "organization": {
                                "_links": {
                                    "self": {
                                        "href": "http://{$host}/api/organizations/{$organization->getId()}"
                                    }
                                }
                            },
                            "activityTypes": []
                        },
                        "_links": {
                            "self": {
                                "href": "http://{$host}/api/camp-types/{$campType->getId()}"
                            }
                        }
                    },
                    "campCollaborations": [],
                    "periods": [],
                    "activityCategories": []
                },
                "_links": {
                    "self": {
                        "href": "http://{$host}/api/camps/{$camp->getId()}"
                    },
                    "activities": {
                        "href": "http://{$host}/api/activities?campId={$camp->getId()}"
                    }
                }
            }
JSON;

        $this->assertEquals(json_decode($expectedResponse), $this->getResponseContent());
    }

    protected function createAndAuthenticateUser() {
        $user = new User();
        $user->setRole(User::ROLE_USER);
        $user->setState(User::STATE_ACTIVATED);

        $this->getEntityManager()->persist($user);

        /** @var AuthenticationService $auth */
        $auth = $this->getApplicationServiceLocator()->get(AuthenticationService::class);
        $auth->getStorage()->write($user->getId());

        return $user;
    }

    // protected function createAndAuthenticateUser() {
    //     /** @var UserService $userService */
    //     $userService = $this->getApplicationServiceLocator()->get(UserService::class);

    //     /** @var AuthenticationService $auth */
    //     $auth = $this->getApplicationServiceLocator()->get(AuthenticationService::class);

    //     $user = $userService->create((object) [
    //         'username' => 'test',
    //         'mailAddress' => 'test@ecamp3.ch',
    //     ]);

    //     $auth->getStorage()->write($user->getId());

    //     return $user;
    // }
}
