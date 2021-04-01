<?php

namespace eCamp\ContentType\MultiSelect;

use Doctrine\ORM\ORMException;
use eCamp\ContentType\MultiSelect\Entity\Option;
use eCamp\ContentType\MultiSelect\Entity\OptionCollection;
use eCamp\ContentType\MultiSelect\Service\OptionService;
use eCamp\Core\ContentType\ContentTypeStrategyBase;
use eCamp\Core\Entity\ContentNode;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\EntityNotFoundException;
use eCamp\Lib\Service\ServiceUtils;

class Strategy extends ContentTypeStrategyBase {
    private OptionService $optionService;

    public function __construct(OptionService $optionService, ServiceUtils $serviceUtils) {
        parent::__construct($serviceUtils);

        $this->optionService = $optionService;
        $this->optionService->setEntityClass(Option::class);
        $this->optionService->setCollectionClass(OptionCollection::class);
    }

    public function contentNodeExtract(ContentNode $contentNode): array {
        $multiSelectItems = $this->optionService->fetchAllByContentNode($contentNode->getId());

        return [
            'options' => $multiSelectItems,
        ];
    }

    /**
     * @throws NoAccessException
     * @throws ORMException
     * @throws EntityNotFoundException
     */
    public function contentNodeCreated(ContentNode $contentNode): void {
        foreach ($contentNode->getContentType()->getJsonConfig()['items'] as $key => $configItem) {
            $option = $this->optionService->createEntity(['pos' => $key, 'translateKey' => $configItem, 'checked' => false], $contentNode);
            $this->getServiceUtils()->emPersist($option);
        }
    }
}
