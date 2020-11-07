<?php

namespace eCamp\ApiTest\Rpc;

use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

/**
 * @internal
 */
class RootTest extends AbstractApiControllerTestCase {
    public function testRootResponse() {
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

    public function testApiResponse() {
        $this->dispatch('/api', 'GET');

        $host = '';
        $expectedResponse = <<<JSON
        {
            "title": "eCamp V3 - API",
            "user": "guest",
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
                "admin": {
                    "href": "http://{$host}/api-tools/ui"
                },
                "users": {
                    "href": "http://{$host}/api/users{/userId}{?page_size,search}",
                    "templated": true
                },
                "campTypes": {
                    "href": "http://{$host}/api/camp-types{/campTypeId}{?page_size}",
                    "templated": true
                },
                "camps": {
                    "href": "http://{$host}/api/camps{/campId}{?page_size}",
                    "templated": true
                },
                "scheduleEntries": {
                    "href": "http://{$host}/api/schedule-entries{/scheduleEntryId}{?page_size,activityId}",
                    "templated": true
                },
                "periods": {
                    "href": "http://{$host}/api/periods{/periodId}{?page_size,campId}",
                    "templated": true
                },
                "activities": {
                    "href": "http://{$host}/api/activities{/activityId}{?page_size,campId}",
                    "templated": true
                }
            }
        }
JSON;

        $this->assertEquals(json_decode($expectedResponse), $this->getResponseContent());
    }
}
