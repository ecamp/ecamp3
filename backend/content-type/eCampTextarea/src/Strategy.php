<?php

namespace eCamp\ContentType\Textarea;

use Doctrine\ORM\ORMException;
use eCamp\ContentType\Textarea\Service\TextareaService;
use eCamp\Core\ContentType\ContentTypeStrategyBase;
use eCamp\Core\Entity\ActivityContent;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\ServiceUtils;
use ZF\Hal\Link\Link;

class Strategy extends ContentTypeStrategyBase {
    /** @var TextareaService */
    private $textareaService;

    public function __construct(TextareaService $textareaService, ServiceUtils $serviceUtils) {
        parent::__construct($serviceUtils);

        $this->textareaService = $textareaService;
    }

    public function activityContentExtract(ActivityContent $activityContent): array {
        $textarea = $this->textareaService->findOneByActivityContent($activityContent->getId());

        if (!$textarea) {
            return [];
        }

        return [
            'textarea' => $textarea,

            /*
            // Alternatively send link only
            'textarea' => Link::factory([
                'rel' => 'textarea',
                'route' => [
                    'name' => 'e-camp-api.rest.doctrine.activity-content.textarea',
                    'params' => [
                        'activityContentId' => $activityContent->getId(),
                        'textareaId' => $textarea->getId(),
                    ],
                ],
            ]),*/
        ];
    }

    /**
     * @throws NoAccessException
     * @throws ORMException
     */
    public function activityContentCreated(ActivityContent $activityContent): void {
        $textarea = $this->textareaService->createEntity([], $activityContent);
        $this->getServiceUtils()->emPersist($textarea);
    }
}
