<?php

namespace eCamp\ContentType\SingleText;

use Doctrine\ORM\ORMException;
use eCamp\ContentType\SingleText\Service\SingleTextService;
use eCamp\Core\ContentType\ContentTypeStrategyBase;
use eCamp\Core\Entity\ActivityContent;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\ServiceUtils;

class Strategy extends ContentTypeStrategyBase {
    private SingleTextService $singleTextService;

    public function __construct(SingleTextService $singleTextService, ServiceUtils $serviceUtils) {
        parent::__construct($serviceUtils);

        $this->singleTextService = $singleTextService;
    }

    public function activityContentExtract(ActivityContent $activityContent): array {
        $singleText = $this->singleTextService->findOneByActivityContent($activityContent->getId());

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
    public function activityContentCreated(ActivityContent $activityContent): void {
        $richtext = $this->singleTextService->createEntity([], $activityContent);
        $this->getServiceUtils()->emPersist($richtext);
    }
}
