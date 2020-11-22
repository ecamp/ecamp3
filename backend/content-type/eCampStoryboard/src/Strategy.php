<?php

namespace eCamp\ContentType\Storyboard;

use Doctrine\ORM\ORMException;
use eCamp\ContentType\Storyboard\Entity\Section;
use eCamp\ContentType\Storyboard\Entity\SectionCollection;
use eCamp\ContentType\Storyboard\Service\SectionService;
use eCamp\Core\ContentType\ContentTypeStrategyBase;
use eCamp\Core\Entity\ActivityContent;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\ApiTools\Hal\Link\Link;

class Strategy extends ContentTypeStrategyBase {
    /** @var SectionService */
    private $sectionService;

    public function __construct(SectionService $sectionService, ServiceUtils $serviceUtils) {
        parent::__construct($serviceUtils);

        $this->sectionService = $sectionService;
    }

    public function activityContentExtract(ActivityContent $activityContent): array {
        $this->sectionService->setEntityClass(Section::class);
        $this->sectionService->setCollectionClass(SectionCollection::class);

        $sections = $this->sectionService->fetchAllByActivityContent($activityContent->getId());

        return [
            'sections' => $sections,

            /*
            // Alternatively send link only
            'sections' => Link::factory([
                'rel' => 'sections',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.activity-content.storyboard',
                    'options' => ['query' => ['activityContentId' => $activityContent->getId()]],
                ],
            ]),*/
        ];
    }

    /**
     * @throws NoAccessException
     * @throws ORMException
     */
    public function activityContentCreated(ActivityContent $activityContent): void {
        $section = $this->sectionService->createEntity(['pos' => 0], $activityContent);
        $this->getServiceUtils()->emPersist($section);
    }
}
