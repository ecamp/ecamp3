<?php

namespace eCamp\ContentType\TextList;

use Doctrine\ORM\ORMException;
use eCamp\ContentType\SingleText\Entity\TextListItem;
use eCamp\ContentType\TextList\Entity\TextListCollection;
use eCamp\ContentType\TextList\Service\TextListItemService;
use eCamp\Core\ContentType\ContentTypeStrategyBase;
use eCamp\Core\Entity\ActivityContent;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\EntityNotFoundException;
use eCamp\Lib\Service\ServiceUtils;

class Strategy extends ContentTypeStrategyBase {
    /** @var TextListItemService */
    private TextListItemService $textListService;

    public function __construct(TextListItemService $textListService, ServiceUtils $serviceUtils) {
        parent::__construct($serviceUtils);

        $this->textListService = $textListService;
    }

    public function activityContentExtract(ActivityContent $activityContent): array {
        $this->textListService->setEntityClass(TextListItem::class);
        $this->textListService->setCollectionClass(TextListCollection::class);
        $listItems = $this->textListService->fetchAllByActivityContent($activityContent->getId());

        return [
            'list' => $listItems,
        ];
    }

    /**
     * @throws NoAccessException
     * @throws ORMException
     * @throws EntityNotFoundException
     */
    public function activityContentCreated(ActivityContent $activityContent): void {
        $section = $this->textListService->createEntity(['pos' => 0], $activityContent);
        $this->getServiceUtils()->emPersist($section);
    }
}
