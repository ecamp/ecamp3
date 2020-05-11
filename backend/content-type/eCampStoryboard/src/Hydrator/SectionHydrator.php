<?php

namespace eCamp\ContentType\Storyboard\Hydrator;

use eCamp\ContentType\Storyboard\Entity\Section;
use Zend\Hydrator\HydratorInterface;
use ZF\Hal\Link\Link;

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
            'text' => $section->getText(),

            'activityContent' => Link::factory([
                'rel' => 'activityContent',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.activity-content',
                    'params' => ['activityContentId' => $section->getActivityContent()->getId()],
                ],
            ]),

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
            ]),
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
        if (isset($data['text'])) {
            $section->setText($data['text']);
        }

        return $section;
    }
}
