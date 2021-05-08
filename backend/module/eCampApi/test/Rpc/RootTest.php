<?php

namespace eCamp\ApiTest\Rpc;

use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

/**
 * @internal
 */
class RootTest extends AbstractApiControllerTestCase {
    public function testRootResponse(): void {
        $this->dispatch('/', 'GET');

        $host = '';
        $expectedResponse = <<<JSON
        {
            "title": "eCamp V3",
            "_links": {
                "self": {
                    "href": "http://{$host}/"
                },
                "api": {
                    "href": "http://{$host}/api"
                },
                "setup": {
                    "href": "http://{$host}/setup.php"
                },
                "php-info": {
                    "href": "http://{$host}/info.php"
                }
            }
        }
JSON;

        $this->assertEquals(json_decode($expectedResponse), $this->getResponseContent());
    }

    public function testApiResponse(): void {
        $this->dispatch('/api', 'GET');

        $host = '';
        $expectedResponse = <<<JSON
        {
            "title": "eCamp V3 - API",
            "authenticated": false,
            "_links": {
                "profile": {
                    "href": "http://{$host}/api/profile"
                },
                "self": {
                    "href": "http://{$host}/api"
                },
                "auth": {
                    "href": "http://{$host}/api/auth"
                },
                "docu": {
                    "href": "http://{$host}/api-tools/swagger"
                },
                "invitation": {
                  "href": "http://{$host}/api/invitations{/inviteKey}{/campCollaborationId}{/action}",
                  "templated": true
                },
                "users": {
                    "href": "http://{$host}/api/users{/userId}{?page_size,page,search}",
                    "templated": true
                },
                "camps": {
                    "href": "http://{$host}/api/camps{/campId}{?page_size,page,isPrototype}",
                    "templated": true
                },
                "campCollaborations": {
                  "href": "http://{$host}/api/camp-collaborations{/campCollaborationId}{?page_size,page,campId,userId}",
                  "templated": true
                },
                "scheduleEntries": {
                    "href": "http://{$host}/api/schedule-entries{/scheduleEntryId}{?page_size,page,activityId}",
                    "templated": true
                },
                "periods": {
                    "href": "http://{$host}/api/periods{/periodId}{?page_size,page,campId}",
                    "templated": true
                },
                "activities": {
                    "href": "http://{$host}/api/activities{/activityId}{?page_size,page,campId,periodId}",
                    "templated": true
                },
                "contentNodes": {
                    "href": "http://{$host}/api/content-nodes{/contentNodeId}{?page_size,page,ownerId,parentId}",
                    "templated": true
                },
                "contentTypes": {
                    "href": "http://{$host}/api/content-types{/contentTypeId}{?page_size,page}",
                    "templated": true
                }
            }
        }
JSON;

        $this->assertEquals(json_decode($expectedResponse), $this->getResponseContent());
    }
}
