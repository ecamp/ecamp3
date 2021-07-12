<?php

namespace App\Tests\Api\ActivityResponsibles;

use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class UpdateActivityResponsibleTest extends ECampApiTestCase {
    public function testPatchActivityResponsibleIsNotAllowed() {
        $activityResponsible = static::$fixtures['activityResponsible1'];
        static::createClientWithCredentials()->request('PATCH', '/activity_responsibles/'.$activityResponsible->getId(), ['json' => [
            'activity' => $this->getIriFor('activity2'),
        ], 'headers' => ['Content-Type' => 'application/merge-patch+json']]);

        $this->assertResponseStatusCodeSame(405); // method not allowed
    }
}
