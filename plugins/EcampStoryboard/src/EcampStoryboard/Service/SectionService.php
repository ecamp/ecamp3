<?php

namespace EcampStoryboard\Service;

use EcampLib\Service\ServiceBase;
use EcampCore\Entity\EventPlugin;

use EcampStoryboard\Entity\Section;
use EcampStoryboard\Repository\SectionRepository;

class SectionService
    extends ServiceBase
{

    /**
     *
     * @var \EcampStoryboard\Repository\SectionRepository
     */
    private $sectionRepository;

    public function __construct(
        SectionRepository $sectionRepository
    ){
        $this->sectionRepository = $sectionRepository;
    }

    public function create(EventPlugin $eventPlugin)
    {
        $section = new Section($eventPlugin);

        $position = $this->sectionRepository->getMaxPosition($eventPlugin) + 1;
        $section->setPosition($position);

        $this->persist($section);

        return $section;
    }

    public function update(Section $section, array $data)
    {
        if (isset($data['duration_in_minutes'])) {
            $section->setDurationInMinutes($data['duration_in_minutes']);
        }
        if (isset($data['text'])) {
            $section->setText($data['text']);
        }
        if (isset($data['info'])) {
            $section->setInfo($data['info']);
        }
    }

    public function delete(Section $section)
    {
        $this->remove($section);
    }

    public function moveUp(Section $section)
    {
        $prevSection = $this->sectionRepository->findPrevSection($section);

        if ($prevSection == null) {
            $this->addValidationMessage("First Section can not be moved up");
        } else {
            $sectionPos = $section->getPosition();

            $section->setPosition($prevSection->getPosition());
            $prevSection->setPosition($sectionPos);
        }
    }

    public function moveDown(Section $section)
    {
        $nextSection = $this->sectionRepository->findNextSection($section);

        if ($nextSection == null) {
            $this->addValidationMessage("Last Section can not be moved down");
        } else {
            $sectionPos = $section->getPosition();

            $section->setPosition($nextSection->getPosition());
            $nextSection->setPosition($sectionPos);
        }
    }

}
