<?php

namespace eCamp\ContentType\Storyboard;

use Doctrine\ORM\ORMException;
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
    }

    public function contentNodeExtract(ContentNode $contentNode): array {
        $sections = $this->sectionService->fetchAllByContentNode($contentNode->getId());

        return [
            'sections' => $sections,

            // add link for embeedded collection
            'sectionsLinks' => Link::factory([
                'rel' => 'sections',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.content-node.storyboard',
                    'options' => ['query' => ['contentNodeId' => $contentNode->getId()]],
                ],
            ]),
        ];
    }

    /**
     * @throws NoAccessException
     * @throws ORMException
     */
    public function contentNodeCreated(ContentNode $contentNode, ?ContentNode $prototype = null): void {
        $data = [];
        if (isset($prototype)) {
            $sections = $this->sectionService->fetchAllByContentNode($prototype->getId());
            foreach ($sections as $s) {
                $data[] = [
                    'pos' => $s->getPos(),
                    'column1' => $s->getColumn1(),
                    'column2' => $s->getColumn2(),
                    'column3' => $s->getColumn3(),
                ];
            }
        }
        if (0 == count($data)) {
            $data[] = ['pos' => 0];
        }

        foreach ($data as $d) {
            $section = $this->sectionService->createEntity($d, $contentNode);
            $this->getServiceUtils()->emPersist($section);
        }
    }
}
