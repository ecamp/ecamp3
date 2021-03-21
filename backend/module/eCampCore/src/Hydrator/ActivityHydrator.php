<?php

namespace eCamp\Core\Hydrator;

use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\ActivityResponsible;
use eCamp\Lib\Entity\EntityLink;
use eCamp\Lib\Entity\EntityLinkCollection;
use eCamp\Lib\Hydrator\Util;
use eCampApi\V1\Rest\CampCollaboration\CampCollaborationCollection;
use eCampApi\V1\Rest\ScheduleEntry\ScheduleEntryCollection;
use Laminas\Hydrator\HydratorInterface;

class ActivityHydrator implements HydratorInterface {
    public static function HydrateInfo(): array {
        return [
            'category' => Util::Entity(function (Activity $e) {
                return $e->getCategory();
            }),
            'campCollaborations' => Util::Collection(function (Activity $e) {
                return new CampCollaborationCollection(
                    $e->getActivityResponsibles()->map(function (ActivityResponsible $ar) {
                        return $ar->getCampCollaboration();
                    })
                );
            }, null),
            'scheduleEntries' => Util::Collection(function (Activity $e) {
                return new ScheduleEntryCollection($e->getScheduleEntries());
            }, null),
        ];
    }

    /**
     * @param object $object
     */
    public function extract($object): array {
        /** @var Activity $activity */
        $activity = $object;

        return [
            'id' => $activity->getId(),
            'title' => $activity->getTitle(),
            'location' => $activity->getLocation(),

            'camp' => new EntityLink($activity->getCamp()),
            'category' => EntityLink::Create($activity->getCategory()),

            'campCollaborations' => new EntityLinkCollection(new CampCollaborationCollection(
                $activity->getActivityResponsibles()->map(function (ActivityResponsible $ar) {
                    return $ar->getCampCollaboration();
                })
            )),

            'scheduleEntries' => new EntityLinkCollection($activity->getScheduleEntries()),

            'contentNodes' => new EntityLinkCollection($activity->getAllContentNodes()),
            'rootContentNode' => EntityLink::Create($activity->getRootContentNode()),
        ];
    }

    /**
     * @param object $object
     */
    public function hydrate(array $data, $object): Activity {
        /** @var Activity $activity */
        $activity = $object;

        if (isset($data['title'])) {
            $activity->setTitle($data['title']);
        }
        if (isset($data['location'])) {
            $activity->setLocation($data['location']);
        }

        return $activity;
    }
}
