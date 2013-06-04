<?php

namespace EcampStoryboard\Serializer;

use EcampApi\Serializer\BaseSerializer;
use EcampStoryboard\Entity\Section;

class SectionSerializer extends BaseSerializer
{
    protected function serialize($section)
    {
        return array(
            'id' 		=> 	$section->getId(),
            'href'		=>	$this->getSectionHref($section),
            'moveUp'	=> 	$this->moveUpLink($section),
            'moveDown'	=> 	$this->moveDownLink($section),
            'position'	=>	$section->getPosition(),
            'duration' 	=> 	$section->getDurationInMinutes(),
            'text'		=> 	$section->getText(),
            'info'		=> 	$section->getInfo(),
        );
    }

    public function getReference(Section $section = null)
    {
        if ($section == null) {
            return null;
        } else {
            return array(
                'id'	=>	$section->getId(),
                'href'	=>	$this->getCampHref($camp)
            );
        }
    }

    private function getSectionHref(Section $section)
    {
        return
            $this->router->assemble(
                array(
                    'controller' => 'sections',
                    'action' => 'get',
                    'pluginInstanceId' => $section->getPluginInstance()->getId(),
                    'id' => $section->getId(),
                    'format' => $this->format
                ),
                array(
                    'name' => 'plugin/storyboard/rest',
                )
            );
    }

    private function moveUpLink(Section $section)
    {
        return
            $this->router->assemble(
                array(
                    'controller' => 'sections',
                    'action' => 'moveUp',
                    'pluginInstanceId' => $section->getPluginInstance()->getId(),
                    'id' => $section->getId(),
                    'format' => $this->format
                ),
                array(
                    'name' => 'plugin/storyboard/default'
                )
            );
    }

    private function moveDownLink(Section $section)
    {
        return
            $this->router->assemble(
                array(
                    'controller' => 'sections',
                    'action' => 'moveDown',
                    'pluginInstanceId' => $section->getPluginInstance()->getId(),
                    'id' => $section->getId(),
                    'format' => $this->format
                ),
                array(
                    'name' => 'plugin/storyboard/default'
                )
            );
    }
}
