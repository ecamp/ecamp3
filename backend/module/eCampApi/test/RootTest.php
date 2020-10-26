<?php

namespace eCamp\ApiTest;

use eCamp\LibTest\PHPUnit\AbstractHttpControllerTestCase;

/**
 * @internal
 */
class RootTest extends AbstractHttpControllerTestCase {
    public function testRootResponse() {
        $headers = $this->getRequest()->getHeaders();
        $headers->addHeaderLine('Accept', 'application/json');
        $headers->addHeaderLine('Content-Type', 'application/json');

        $this->dispatch('/api', 'GET');

        //$req = $this->getRequest();
        $resp = $this->getResponse();

        //$baseUrl = $req->getBaseUrl();
        //$basePath = $req->getBasePath();

        $host = "";

        $expectedResponse = <<<JSON
        {
            "title": "eCamp V3 - API",
            "user": "guest",
            "authenticated": false,
            "_links": {
                "profile": {
                    "href": "http://$host/api/profile"
                },
                "self": {
                    "href": "http://$host/api"
                },
                "auth": {
                    "href": "http://$host/api/auth"
                },
                "docu": {
                    "href": "http://$host/api-tools/swagger"
                },
                "admin": {
                    "href": "http://$host/api-tools/ui"
                },
                "users": {
                    "href": "http://$host/api/users{/userId}{?search,page_size}",
                    "templated": true
                },
                "campTypes": {
                    "href": "http://$host/api/camp-types{/campTypeId}{?page_size}",
                    "templated": true
                },
                "camps": {
                    "href": "http://$host/api/camps{/campId}{?page_size}",
                    "templated": true
                },
                "scheduleEntries": {
                    "href": "http://$host/api/schedule-entries{/scheduleEntryId}{?activityId,page_size}",
                    "templated": true
                }
            }
        }
JSON;

        $this->assertEquals(json_decode($expectedResponse), json_decode($resp->getContent()));
        
    }
}
