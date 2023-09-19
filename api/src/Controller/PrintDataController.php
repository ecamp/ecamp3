<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\ActivityProgressLabel;
use App\Entity\ActivityResponsible;
use App\Entity\Camp;
use App\Entity\CampCollaboration;
use App\Entity\Category;
use App\Entity\ContentNode;
use App\Entity\ContentType;
use App\Entity\Day;
use App\Entity\DayResponsible;
use App\Entity\MaterialItem;
use App\Entity\MaterialList;
use App\Entity\Period;
use App\Entity\ScheduleEntry;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Context\Normalizer\JsonSerializableNormalizerContextBuilder;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PrintDataController extends AbstractController {
    private array $contextBuilder;

    public function __construct(
        private EntityManagerInterface $em,
        private NormalizerInterface $normalizer
    ) {
        $this->contextBuilder = (new JsonSerializableNormalizerContextBuilder())
            ->withContext(
                (new ObjectNormalizerContextBuilder())
                    ->withContext([])
                    ->withGroups(['print'])
            )
            ->toArray()
        ;
    }

    #[Route('/print/camp/{campId}', 'print-camp')]
    public function camp($campId) {
        // General-Data
        $contentTypesJson = $this->getContentTypeData();

        // Camp-Data
        $camp = $this->getCampData($campId);
        $campCollaborations = $this->getCampCollaborationData($campId);
        $users = $this->getUserData($campId);
        $categories = $this->getCategoryData($campId);
        $activityProgressLabels = $this->getActivityProgressLabelData($campId);
        $periods = $this->getPeriodData($campId);
        $days = $this->getDayData($campId);
        $dayResponsibles = $this->getDayResponsibleData($campId);
        $activities = $this->getActivityData($campId);
        $activityResponsibles = $this->getActivityResponsibleData($campId);
        $scheduleEntries = $this->getScheduleEntryData($campId);
        $contentNodes = $this->getContentNodeData($campId);
        $materialLists = $this->getMaterialListData($campId);
        $materialItems = $this->getMaterialItemData($campId);

        return new \Symfony\Component\HttpFoundation\JsonResponse([
            'camp' => array_merge_recursive(
                $camp,
                $this->createLinkCollection('campCollaborations', $campCollaborations),
                $this->createLinkCollection('categories', $categories),
                $this->createLinkCollection('activityProgressLabels', $activityProgressLabels),
                $this->createLinkCollection('periods', $periods),
                $this->createLinkCollection('activities', $activities),
                $this->createLinkCollection('materialLists', $materialLists),
            ),
            'campCollaborations' => array_map(
                fn ($c) => array_merge_recursive(
                    $c,
                    $this->createLinkCollectionFiltered($c, 'activityResponsibles', $activityResponsibles, 'campCollaboration'),
                    $this->createLinkCollectionFiltered($c, 'dayResponsibles', $dayResponsibles, 'campCollaboration'),
                ),
                $campCollaborations,
            ),
            'users' => $users,
            'categories' => $categories,
            'activityProgressLabels' => $activityProgressLabels,
            'contentTypes' => $contentTypesJson,

            'periods' => array_map(
                fn ($p) => array_merge_recursive(
                    $p,
                    $this->createLinkCollectionFiltered($p, 'days', $days, 'period'),
                    $this->createLinkCollectionFiltered($p, 'scheduleEntries', $scheduleEntries, 'period'),
                ),
                $periods
            ),
            'days' => array_map(
                fn ($d) => array_merge_recursive(
                    $d,
                    $this->createLinkCollectionFiltered($d, 'dayResponsibles', $dayResponsibles, 'day'),
                ),
                $days,
            ),
            'dayResponsibles' => $dayResponsibles,

            'activities' => array_map(
                fn ($a) => array_merge_recursive(
                    $a,
                    $this->createLinkCollectionFiltered($a, 'activityResponsibles', $activityResponsibles, 'activity'),
                    $this->createLinkCollectionFiltered($a, 'scheduleEntries', $scheduleEntries, 'activity'),
                ),
                $activities
            ),
            'activityResponsibles' => $activityResponsibles,
            'scheduleEntries' => $scheduleEntries,
            'contentNodes' => array_map(
                fn ($c) => array_merge_recursive(
                    $c,
                    $this->createLinkCollectionFiltered($c, 'children', $contentNodes, 'parent'),
                    ('Material' == $c['contentTypeName']) ? $this->createLinkCollectionFiltered($c, 'materialItems', $materialItems, 'materialNode') : []
                ),
                $contentNodes
            ),

            'materialLists' => array_map(
                fn ($ml) => array_merge_recursive(
                    $ml,
                    $this->createLinkCollectionFiltered($ml, 'materialItems', $materialItems, 'materialList')
                ),
                $materialLists,
            ),
            'materialItems' => $materialItems,
        ]);
    }

    private function getContentTypeData(): array {
        $contentTypes = $this->em->createQueryBuilder()
            ->select('c')
            ->from(ContentType::class, 'c')
            ->getQuery()
            ->getResult()
        ;

        return $this->normalizer->normalize($contentTypes, 'jsonhal', $this->contextBuilder);
    }

    private function getCampData($campId): array {
        /** @var Camp */
        $camp = $this->em->find(Camp::class, $campId);

        return $this->normalizer->normalize($camp, 'jsonhal', $this->contextBuilder);
    }

    private function getCampCollaborationData($campId): array {
        $campCollaborations = $this->em->createQueryBuilder()
            ->select('c')
            ->from(CampCollaboration::class, 'c')
            ->where('c.camp = ?1')
            ->setParameter(1, $campId)
            ->getQuery()
            ->getResult()
        ;

        return $this->normalizer->normalize($campCollaborations, 'jsonhal', $this->contextBuilder);
    }

    private function getUserData($campId): array {
        $users = $this->em->createQueryBuilder()
            ->select('u')
            ->from(User::class, 'u')
            ->join('u.collaborations', 'c')
            ->where('c.camp = ?1')
            ->setParameter(1, $campId)
            ->getQuery()
            ->getResult()
        ;

        return $this->normalizer->normalize($users, 'jsonhal', $this->contextBuilder);
    }

    private function getCategoryData($campId): array {
        $categories = $this->em->createQueryBuilder()
            ->select('c')
            ->from(Category::class, 'c')
            ->where('c.camp = ?1')
            ->setParameter(1, $campId)
            ->getQuery()
            ->getResult()
        ;

        return $this->normalizer->normalize($categories, 'jsonhal', $this->contextBuilder);
    }

    private function getActivityProgressLabelData($campId): array {
        $activityProgressLabels = $this->em->createQueryBuilder()
            ->select('l')
            ->from(ActivityProgressLabel::class, 'l')
            ->where('l.camp = ?1')
            ->setParameter(1, $campId)
            ->getQuery()
            ->getResult()
        ;

        return $this->normalizer->normalize($activityProgressLabels, 'jsonhal', $this->contextBuilder);
    }

    private function getPeriodData($campId): array {
        $periods = $this->em->createQueryBuilder()
            ->select('p')
            ->from(Period::class, 'p')
            ->where('p.camp = ?1')
            ->setParameter(1, $campId)
            ->getQuery()
            ->getResult()
        ;

        return $this->normalizer->normalize($periods, 'jsonhal', $this->contextBuilder);
    }

    private function getDayData($campId): array {
        $days = $this->em->createQueryBuilder()
            ->select('d')
            ->from(Day::class, 'd')
            ->join('d.period', 'p')
            ->where('p.camp = ?1')
            ->setParameter(1, $campId)
            ->getQuery()
            ->getResult()
        ;

        return $this->normalizer->normalize($days, 'jsonhal', $this->contextBuilder);
    }

    private function getDayResponsibleData($campId): array {
        $dayResponsibles = $this->em->createQueryBuilder()
            ->select('dr')
            ->from(DayResponsible::class, 'dr')
            ->join('dr.day', 'd')
            ->join('d.period', 'p')
            ->where('p.camp = ?1')
            ->setParameter(1, $campId)
            ->getQuery()
            ->getResult()
        ;

        return $this->normalizer->normalize($dayResponsibles, 'jsonhal', $this->contextBuilder);
    }

    private function getActivityData($campId): array {
        $activities = $this->em->createQueryBuilder()
            ->select('a')
            ->from(Activity::class, 'a')
            ->where('a.camp = ?1')
            ->setParameter(1, $campId)
            ->getQuery()
            ->getResult()
        ;

        return $this->normalizer->normalize($activities, 'jsonhal', $this->contextBuilder);
    }

    private function getActivityResponsibleData($campId): array {
        $activityResponsibles = $this->em->createQueryBuilder()
            ->select('r')
            ->from(ActivityResponsible::class, 'r')
            ->join('r.activity', 'a')
            ->where('a.camp = ?1')
            ->setParameter(1, $campId)
            ->getQuery()
            ->getResult()
        ;

        return $this->normalizer->normalize($activityResponsibles, 'jsonhal', $this->contextBuilder);
    }

    private function getScheduleEntryData($campId): array {
        $scheduleEntries = $this->em->createQueryBuilder()
            ->select('s')
            ->from(ScheduleEntry::class, 's')
            ->join('s.activity', 'a')
            ->where('a.camp = ?1')
            ->setParameter(1, $campId)
            ->getQuery()
            ->getResult()
        ;

        return $this->normalizer->normalize($scheduleEntries, 'jsonhal', $this->contextBuilder);
    }

    private function getContentNodeData($campId): array {
        $contentNodes = $this->em->createQueryBuilder()
            ->select('c')
            ->from(ContentNode::class, 'c')
            ->join(Activity::class, 'a', Join::WITH, 'a.rootContentNode = c.root')
            ->where('a.camp = ?1')
            ->setParameter(1, $campId)
            ->getQuery()
            ->getResult()
        ;

        return $this->normalizer->normalize($contentNodes, 'jsonhal', $this->contextBuilder);
    }

    private function getMaterialListData($campId): array {
        $materialLists = $this->em->createQueryBuilder()
            ->select('m')
            ->from(MaterialList::class, 'm')
            ->where('m.camp = ?1')
            ->setParameter(1, $campId)
            ->getQuery()
            ->getResult()
        ;

        return $this->normalizer->normalize($materialLists, 'jsonhal', $this->contextBuilder);
    }

    private function getMaterialItemData($campId): array {
        $materialItems = $this->em->createQueryBuilder()
            ->select('mi')
            ->from(MaterialItem::class, 'mi')
            ->join('mi.materialList', 'm')
            ->where('m.camp = ?1')
            ->setParameter(1, $campId)
            ->getQuery()
            ->getResult()
        ;

        return $this->normalizer->normalize($materialItems, 'jsonhal', $this->contextBuilder);
    }

    private function createLinkCollection($listName, $list) {
        return [
            '_embedded' => [
                $listName => array_map(
                    function ($p) {
                        return [
                            '_links' => [
                                'self' => [
                                    'href' => $p['_links']['self']['href'],
                                ],
                            ],
                        ];
                    },
                    $list
                ),
            ],
        ];
    }

    private function createLinkCollectionFiltered($item, $listName, $childrenList, $parentLink) {
        $children = [];
        foreach ($childrenList as $child) {
            if (null != $child['_links'][$parentLink]) {
                if ($child['_links'][$parentLink]['href'] == $item['_links']['self']['href']) {
                    $children[] = $child;
                }
            }
        }

        return $this->createLinkCollection($listName, $children);
    }
}
