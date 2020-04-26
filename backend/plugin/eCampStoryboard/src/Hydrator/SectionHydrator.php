<?php

namespace eCamp\Plugin\Storyboard\Hydrator;

use eCamp\Plugin\Storyboard\Entity\Section;
use Zend\Hydrator\HydratorInterface;
use ZF\Hal\Link\Link;

class SectionHydrator implements HydratorInterface {
    /**
     * @param object $object
     * @return array
     */
    public function extract($object) {
        /** @var Section $section */
        $section = $object;

        return [
            'id' => $section->getId(),
            'pos' => $section->getPos(),
            'text' => $section->getText(),
            
            'event_plugin' => Link::factory([
                'rel' => 'event_plugin',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.event-plugin',
                    'params' => [ 'event_plugin_id' => $section->getEventPlugin()->getId() ]
                ]
            ]),

            'move_up' => Link::factory([
                'rel' => 'move_up',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.event-plugin.storyboard/move',
                    'params' => [
                        'section_id' => $section->getId(),
                        'action' => 'move_up'
                    ],
                ]
            ]),
            'move_down' => Link::factory([
                'rel' => 'move_down',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.event-plugin.storyboard/move',
                    'params' => [
                        'section_id' => $section->getId(),
                        'action' => 'move_down'
                    ],
                ]
            ]),
        ];
    }

    /**
     * @param array $data
     * @param object $object
     * @return object
     */
    public function hydrate(array $data, $object) {
        /** @var Section $section */
        $section = $object;

        $section->setPos($data['pos']);
        $section->setText($data['text']);

        return $section;
    }
}
