<?php

namespace eCamp\ContentType\Storyboard;

use Doctrine\ORM\ORMException;
use eCamp\ContentType\Storyboard\Entity\Section;
use eCamp\ContentType\Storyboard\Entity\SectionCollection;
use eCamp\ContentType\Storyboard\Service\SectionService;
use eCamp\Core\ContentType\ContentTypeStrategyBase;
use eCamp\Core\Entity\ContentNode;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\ApiTools\Hal\Link\Link;

class Strategy extends ContentTypeStrategyBase {
    /** @var SectionService */
    private $sectionService;

    public function __construct(SectionService $sectionService, ServiceUtils $serviceUtils) {
        parent::__construct($serviceUtils);

        $this->sectionService = $sectionService;
        $this->sectionService->setEntityClass(Section::class);
        $this->sectionService->setCollectionClass(SectionCollection::class);
    }

    public function contentNodeExtract(ContentNode $contentNode): array {
        $sections = $this->sectionService->fetchAllByContentNode($contentNode->getId());

        return [
            'sections' => $sections,

            /*
            // Alternatively send link only
            'sections' => Link::factory([
                'rel' => 'sections',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.content-node.storyboard',
                    'options' => ['query' => ['contentNodeId' => $contentNode->getId()]],
                ],
            ]),*/
        ];
    }

    /**
     * @throws NoAccessException
     * @throws ORMException
     */
    public function contentNodeCreated(ContentNode $contentNode): void {
        $section = $this->sectionService->createEntity(['pos' => 0], $contentNode);
        $this->getServiceUtils()->emPersist($section);
    }
}
