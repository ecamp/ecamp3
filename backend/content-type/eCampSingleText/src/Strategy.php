<?php

namespace eCamp\ContentType\SingleText;

use Doctrine\ORM\ORMException;
use eCamp\ContentType\SingleText\Entity\SingleText;
use eCamp\ContentType\SingleText\Service\SingleTextService;
use eCamp\Core\ContentType\ContentTypeStrategyBase;
use eCamp\Core\Entity\ContentNode;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\ServiceUtils;

class Strategy extends ContentTypeStrategyBase {
    private SingleTextService $singleTextService;

    public function __construct(SingleTextService $singleTextService, ServiceUtils $serviceUtils) {
        parent::__construct($serviceUtils);

        $this->singleTextService = $singleTextService;
    }

    public function contentNodeExtract(ContentNode $contentNode): array {
        $singleText = $this->singleTextService->findOneByContentNode($contentNode->getId());

        if (!$singleText) {
            return [];
        }

        return [
            'singleText' => $singleText,
        ];
    }

    /**
     * @throws NoAccessException
     * @throws ORMException
     */
    public function contentNodeCreated(ContentNode $contentNode, ?ContentNode $prototype = null): void {
        $data = [];
        if (isset($prototype)) {
            /** @var SingleText $singleText */
            $singleText = $this->singleTextService->findOneByContentNode($prototype->getId());
            $data = ['text' => $singleText->getText()];
        }
        $richtext = $this->singleTextService->createEntity($data, $contentNode);
        $this->getServiceUtils()->emPersist($richtext);
    }
}
