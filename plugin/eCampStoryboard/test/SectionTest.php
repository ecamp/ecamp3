<?php

namespace eCamp\Plugin\StoryboardTest;

use eCamp\LibTest\PHPUnit\AbstractHttpControllerTestCase;

class SectionTest extends AbstractHttpControllerTestCase
{
    public function testSectionMoveUp() {

        $headers = $this->getRequest()->getHeaders();
        $headers->addHeaderLine('Accept', 'application/json');
        $headers->addHeaderLine('Content-Type', 'application/json');

        $req  = $this->getRequest();
        $this->dispatch("/api/event_plugin/b6612b43/section/b6612b41/move_up", 'GET');

        $req  = $this->getRequest();
        $resp = $this->getResponse();

        $baseUrl = $req->getBaseUrl();
        $basePath = $req->getBasePath();

        $this->assertNotRedirect();
    }
}
