<?php

namespace eCamp\ContentType\Storyboard\Hydrator;

use eCamp\ContentType\Storyboard\Entity\Section;
use Laminas\ApiTools\Hal\Link\Link;
use Laminas\Hydrator\HydratorInterface;

class SectionHydrator implements HydratorInterface {
    /**
     * @param object $object
     *
     * @return array
     */
    public function extract($object) {
        /** @var Section $section */
        $section = $object;

        return [
            'id' => $section->getId(),
            'pos' => $section->getPos(),
            'column1' => $section->getColumn1(),
            'column2' => $section->getColumn2(),
            'column3' => $section->getColumn3(),

            'activityContent' => Link::factory([
                'rel' => 'activityContent',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.activity-content',
                    'params' => ['activityContentId' => $section->getActivityContent()->getId()],
                ],
            ]),

            /*
            'move_up' => Link::factory([
                'rel' => 'move_up',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.activity-content.storyboard/move',
                    'params' => [
                        'sectionId' => $section->getId(),
                        'action' => 'move_up',
                    ],
                ],
            ]),
            'move_down' => Link::factory([
                'rel' => 'move_down',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.activity-content.storyboard/move',
                    'params' => [
                        'sectionId' => $section->getId(),
                        'action' => 'move_down',
                    ],
                ],
            ]),*/
        ];
    }

    /**
     * @param object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var Section $section */
        $section = $object;

        if (isset($data['pos'])) {
            $section->setPos($data['pos']);
        }

        if (isset($data['column1'])) {
            $section->setColumn1($data['column1']);
        }

        if (isset($data['column2'])) {
            $section->setColumn2($data['column2']);
        }

        if (isset($data['column3'])) {
            $section->setColumn3($data['column3']);
        }

        return $section;
    }
}
