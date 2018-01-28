<?php

namespace eCamp\Plugin\Storyboard\Hydrator;

use eCamp\Plugin\Storyboard\Entity\Section;
use Zend\Hydrator\HydratorInterface;
use ZF\Hal\Link\Link;

class SectionHydrator implements HydratorInterface
{
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
            'event_plugin' => $section->getEventPlugin(),

            'move_up' => Link::factory([
                'rel' => 'move_up',
                'route' => [
                    'name' => 'ecamp.api.event_plugin/ecamp.section/move',
                    'params' => [
                        'section_id' => $section->getId(),
                        'action' => 'move_up'
                    ],
                ]
            ]),
            'move_down' => Link::factory([
                'rel' => 'move_down',
                'route' => [
                    'name' => 'ecamp.api.event_plugin/ecamp.section/move',
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
