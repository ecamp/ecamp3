<?php

namespace eCamp\ContentType\Richtext;

use Doctrine\ORM\ORMException;
use eCamp\ContentType\Richtext\Service\RichtextService;
use eCamp\Core\ContentType\ContentTypeStrategyBase;
use eCamp\Core\Entity\ActivityContent;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\ServiceUtils;

class Strategy extends ContentTypeStrategyBase {
    /** @var RichtextService */
    private $richtextService;

    public function __construct(RichtextService $richtextService, ServiceUtils $serviceUtils) {
        parent::__construct($serviceUtils);

        $this->richtextService = $richtextService;
    }

    public function activityContentExtract(ActivityContent $activityContent): array {
        $richtext = $this->richtextService->findOneByActivityContent($activityContent->getId());

        if (!$richtext) {
            return [];
        }

        return [
            'richtext' => $richtext,
        ];
    }

    /**
     * @throws NoAccessException
     * @throws ORMException
     */
    public function activityContentCreated(ActivityContent $activityContent): void {
        $richtext = $this->richtextService->createEntity([], $activityContent);
        $this->getServiceUtils()->emPersist($richtext);
    }
}
