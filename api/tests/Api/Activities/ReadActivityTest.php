<?php

namespace App\Tests\Api\Activities;

use App\Entity\Activity;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class ReadActivityTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function testGetSingleActivityIsAllowedForCollaborator() {
        /** @var Activity $activity */
        $activity = static::$fixtures['activity1'];
        static::createClientWithCredentials()->request('GET', '/activities/'.$activity->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $activity->getId(),
            'title' => $activity->title,
            'location' => $activity->location,
            '_links' => [
                'rootContentNode' => ['href' => $this->getIriFor('contentNode1')],
                //'contentNodes' => ['href' => '/content_nodes?owner=/activities/'.$activity->getId()],
                'category' => ['href' => $this->getIriFor('category1')],
                'camp' => ['href' => $this->getIriFor('camp1')],
                'scheduleEntries' => ['href' => '/schedule_entries?activity=/activities/'.$activity->getId()],
                //'campCollaborations' => ['href' => '/camp_collaborations?activity=/activities/'.$activity->getId()],
            ],
        ]);
    }
}
