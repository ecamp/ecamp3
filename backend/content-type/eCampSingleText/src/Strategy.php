<?php

namespace eCamp\ContentType\SingleText;

use Doctrine\ORM\ORMException;
use eCamp\ContentType\SingleText\Entity\SingleText;
use eCamp\ContentType\SingleText\Entity\SingleTextCollection;
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
        $this->singleTextService->setEntityClass(SingleText::class);
        $this->singleTextService->setCollectionClass(SingleTextCollection::class);
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
    public function contentNodeCreated(ContentNode $contentNode): void {
        $richtext = $this->singleTextService->createEntity([], $contentNode);
        $this->getServiceUtils()->emPersist($richtext);
    }
}
