<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use eCamp\Core\Entity\ActivityCategory;
use eCamp\Core\Entity\ActivityCategoryTemplate;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\ContentTypeConfigTemplate;
use eCamp\Core\Hydrator\ActivityCategoryHydrator;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Service\EntityNotFoundException;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

class ActivityCategoryService extends AbstractEntityService {
    /** @var ContentTypeConfigService */
    protected $contentTypeConfigService;

    public function __construct(
        ServiceUtils $serviceUtils,
        AuthenticationService $authenticationService,
        ContentTypeConfigService $contentTypeConfigService
    ) {
        parent::__construct(
            $serviceUtils,
            ActivityCategory::class,
            ActivityCategoryHydrator::class,
            $authenticationService
        );

        $this->contentTypeConfigService = $contentTypeConfigService;
    }

    public function createFromTemplate(Camp $camp, ActivityCategoryTemplate $template) {
        /** @var ActivityCategory $activityCategory */
        $activityCategory = $this->create((object) [
            'campId' => $camp->getId(),
            'short' => $template->getShort(),
            'name' => $template->getName(),
            'color' => $template->getColor(),
            'numberingStyle' => $template->getNumberingStyle(),
        ]);
        $activityCategory->setActivityCategoryTemplateId($template->getId());

        /** @var ContentTypeConfigTemplate $contentTypeConfigTemplate */
        foreach ($template->getContentTypeConfigTemplates() as $contentTypeConfigTemplate) {
            $this->contentTypeConfigService->createFromTemplate($activityCategory, $contentTypeConfigTemplate);
        }

        return $activityCategory;
    }

    /**
     * @param mixed $data
     *
     * @throws EntityNotFoundException
     * @throws ORMException
     * @throws NoAccessException
     *
     * @return ActivityCategory
     */
    protected function createEntity($data) {
        /** @var ActivityCategory $activityCategory */
        $activityCategory = parent::createEntity($data);

        /** @var Camp $camp */
        $camp = $this->findRelatedEntity(Camp::class, $data, 'campId');
        $camp->addActivityCategory($activityCategory);

        return $activityCategory;
    }

    protected function fetchAllQueryBuilder($params = []) {
        $q = parent::fetchAllQueryBuilder($params);
        $q->andWhere($this->createFilter($q, Camp::class, 'row', 'camp'));

        if (isset($params['campId'])) {
            $q->andWhere('row.camp = :campId');
            $q->setParameter('campId', $params['campId']);
        }

        return $q;
    }

    protected function fetchQueryBuilder($id) {
        $q = parent::fetchQueryBuilder($id);
        $q->andWhere($this->createFilter($q, Camp::class, 'row', 'camp'));

        return $q;
    }
}
