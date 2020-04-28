<?php

namespace eCamp\Plugin\Storyboard\Service;

use Doctrine\Common\Collections\ArrayCollection;
use eCamp\Core\Plugin\BasePluginService;
use eCamp\Lib\Service\ServiceUtils;
use eCamp\Plugin\Storyboard\Entity\Section;
use eCamp\Plugin\Storyboard\Hydrator\SectionHydrator;
use Zend\Authentication\AuthenticationService;

class SectionService extends BasePluginService {
    public function __construct(ServiceUtils $serviceUtils, AuthenticationService $authenticationService) {
        parent::__construct(
            $serviceUtils,
            Section::class,
            SectionHydrator::class,
            $authenticationService
        );
    }

    public function moveUp($id) {
        /** @var Section $section2 */
        $section2 = $this->findEntity(Section::class, $id);

        $q = $this->getRepository()->createQueryBuilder('row');
        $q->andWhere('row.eventPlugin = :eventPluginId');
        $q->setParameter('eventPluginId', $section2->getEventPlugin()->getId());
        $q->andWhere('row.pos < :pos');
        $q->setParameter('pos', $section2->getPos());
        $q->orderBy('row.pos', 'DESC');
        $q->setMaxResults(1);

        /** @var ArrayCollection $section1Coll */
        $section1Coll = $q->getQuery()->getResult();

        if (count($section1Coll) > 0) {
            /** @var Section $section1 */
            $section1 = $section1Coll[0];

            $pos1 = $section1->getPos();
            $pos2 = $section2->getPos();

            $section1->setPos($pos2);
            $section2->setPos($pos1);
        }
    }

    public function moveDown($id) {
        /** @var Section $section1 */
        $section1 = $this->findEntity(Section::class, $id);

        $q = $this->getRepository()->createQueryBuilder('row');
        $q->andWhere('row.eventPlugin = :eventPluginId');
        $q->setParameter('eventPluginId', $section1->getEventPlugin()->getId());
        $q->andWhere('row.pos > :pos');
        $q->setParameter('pos', $section1->getPos());
        $q->orderBy('row.pos', 'ASC');
        $q->setMaxResults(1);

        /** @var ArrayCollection $section2Coll */
        $section2Coll = $q->getQuery()->getResult();

        if (count($section2Coll) > 0) {
            /** @var Section $section2 */
            $section2 = $section2Coll[0];

            $pos1 = $section1->getPos();
            $pos2 = $section2->getPos();

            $section1->setPos($pos2);
            $section2->setPos($pos1);
        }
    }

    protected function fetchAllQueryBuilder($params = []) {
        $q = parent::fetchAllQueryBuilder($params);
        $q->orderBy('row.pos');

        return $q;
    }
}
