<?php

namespace eCamp\ContentType\MultiSelect;

use Doctrine\ORM\ORMException;
use eCamp\ContentType\MultiSelect\Entity\Option;
use eCamp\ContentType\MultiSelect\Entity\OptionCollection;
use eCamp\ContentType\MultiSelect\Service\OptionService;
use eCamp\Core\ContentType\ContentTypeStrategyBase;
use eCamp\Core\Entity\ActivityContent;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\EntityNotFoundException;
use eCamp\Lib\Service\ServiceUtils;

class Strategy extends ContentTypeStrategyBase {
    private OptionService $optionService;

    public function __construct(OptionService $optionService, ServiceUtils $serviceUtils) {
        parent::__construct($serviceUtils);

        $this->optionService = $optionService;
    }

    public function activityContentExtract(ActivityContent $activityContent): array {
        $this->optionService->setEntityClass(Option::class);
        $this->optionService->setCollectionClass(OptionCollection::class);
        $multiSelectItems = $this->optionService->fetchAllByActivityContent($activityContent->getId());

        return [
            'options' => $multiSelectItems,
        ];
    }

    /**
     * @throws NoAccessException
     * @throws ORMException
     * @throws EntityNotFoundException
     */
    public function activityContentCreated(ActivityContent $activityContent): void {
        foreach ($activityContent->getContentType()->getJsonConfig()['items'] as $key => $configItem) {
            $option = $this->optionService->createEntity(['pos' => $key, 'translateKey' => $configItem, 'checked' => false], $activityContent);
            $this->getServiceUtils()->emPersist($option);
        }
    }
}
