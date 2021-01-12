<?php

namespace eCamp\ContentType\MultiSelect;

use Doctrine\ORM\ORMException;
use eCamp\ContentType\MultiSelect\Entity\MultiSelectItem;
use eCamp\ContentType\MultiSelect\Entity\MultiSelectItemCollection;
use eCamp\ContentType\MultiSelect\Service\MultiSelectItemService;
use eCamp\Core\ContentType\ContentTypeStrategyBase;
use eCamp\Core\Entity\ActivityContent;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\EntityNotFoundException;
use eCamp\Lib\Service\ServiceUtils;

class Strategy extends ContentTypeStrategyBase {
    /** @var MultiSelectItemService */
    private MultiSelectItemService $multiSelectService;

    public function __construct(MultiSelectItemService $multiSelectService, ServiceUtils $serviceUtils) {
        parent::__construct($serviceUtils);

        $this->multiSelectService = $multiSelectService;
    }

    public function activityContentExtract(ActivityContent $activityContent): array {
        $this->multiSelectService->setEntityClass(MultiSelectItem::class);
        $this->multiSelectService->setCollectionClass(MultiSelectItemCollection::class);
        $multiSelectItems = $this->multiSelectService->fetchAllByActivityContent($activityContent->getId());

        return [
            'items' => $multiSelectItems,
        ];
    }

    /**
     * @throws NoAccessException
     * @throws ORMException
     * @throws EntityNotFoundException
     */
    public function activityContentCreated(ActivityContent $activityContent): void {
        $section = $this->multiSelectService->createEntity(['pos' => 0], $activityContent);
        $this->getServiceUtils()->emPersist($section);
    }
}
