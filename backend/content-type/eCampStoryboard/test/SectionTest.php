<?php

namespace eCamp\ContentType\StoryboardTest;

use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

/**
 * @internal
 */
class SectionTest extends AbstractApiControllerTestCase {
    public function testSectionMoveUp(): void {
        $this->dispatch('/api/activity-content/b6612b43/section/b6612b41/move_up', 'GET');

        $req = $this->getRequest();
        $resp = $this->getResponse();

        $baseUrl = $req->getBaseUrl();
        $basePath = $req->getBasePath();

        $this->assertNotRedirect();
    }
}
