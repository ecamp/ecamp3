<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\ActivityProgressLabel;
use App\Entity\ActivityResponsible;
use App\Entity\Camp;
use App\Entity\CampCollaboration;
use App\Entity\Category;
use App\Entity\Day;
use App\Entity\Period;
use App\Entity\ScheduleEntry;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Context\Normalizer\JsonSerializableNormalizerContextBuilder;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PrintDataController extends AbstractController {
    public function __construct(
        private EntityManagerInterface $em,
        private NormalizerInterface $normalizer
    ) {
    }

    #[Route('/print/camp/{campId}', 'print-camp')]
    public function camp($campId) {
        // api_platform.hal.normalizer.item

        /** @var Camp */
        $camp = $this->em->find(Camp::class, $campId);

        $q = $this->em->createQueryBuilder();
        $q->select('c');
        $q->from(CampCollaboration::class, 'c');
        $q->where('c.camp = ?1');
        $q->setParameter(1, $campId);
        $campCollaborations = $q->getQuery()->getResult();

        $q = $this->em->createQueryBuilder();
        $q->select('u');
        $q->from(User::class, 'u');
        $q->join('u.collaborations', 'c');
        $q->where('c.camp = ?1');
        $q->setParameter(1, $campId);
        $users = $q->getQuery()->getResult();

        $q = $this->em->createQueryBuilder();
        $q->select('p');
        $q->from(Period::class, 'p');
        $q->where('p.camp = ?1');
        $q->setParameter(1, $campId);
        $periods = $q->getQuery()->getResult();

        $q = $this->em->createQueryBuilder();
        $q->select('d');
        $q->from(Day::class, 'd');
        $q->join('d.period', 'p');
        $q->where('p.camp = ?1');
        $q->setParameter(1, $campId);
        $days = $q->getQuery()->getResult();

        $q = $this->em->createQueryBuilder();
        $q->select('c');
        $q->from(Category::class, 'c');
        $q->where('c.camp = ?1');
        $q->setParameter(1, $campId);
        $categories = $q->getQuery()->getResult();

        $q = $this->em->createQueryBuilder();
        $q->select('a');
        $q->from(Activity::class, 'a');
        $q->where('a.camp = ?1');
        $q->setParameter(1, $campId);
        $activities = $q->getQuery()->getResult();

        $q = $this->em->createQueryBuilder();
        $q->select('s');
        $q->from(ScheduleEntry::class, 's');
        $q->join('s.activity', 'a');
        $q->where('a.camp = ?1');
        $q->setParameter(1, $campId);
        $scheduleEntries = $q->getQuery()->getResult();

        $q = $this->em->createQueryBuilder();
        $q->select('l');
        $q->from(ActivityProgressLabel::class, 'l');
        $q->where('l.camp = ?1');
        $q->setParameter(1, $campId);
        $activityProgressLabels = $q->getQuery()->getResult();

        $q = $this->em->createQueryBuilder();
        $q->select('r');
        $q->from(ActivityResponsible::class, 'r');
        $q->join('r.activity', 'a');
        $q->where('a.camp = ?1');
        $q->setParameter(1, $campId);
        $activityResponsibles = $q->getQuery()->getResult();

        $contextBuilder = (new ObjectNormalizerContextBuilder())
            ->withContext([])
            ->withGroups(['print'])
        ;
        $contextBuilder = (new JsonSerializableNormalizerContextBuilder())
            ->withContext($contextBuilder)
        ;

        $campJson = $this->normalizer->normalize($camp, 'jsonhal', $contextBuilder->toArray());
        $campCollaborationsJons = $this->normalizer->normalize($campCollaborations, 'jsonhal', $contextBuilder->toArray());
        $usersJson = $this->normalizer->normalize($users, 'jsonhal', $contextBuilder->toArray());
        $periodsJson = $this->normalizer->normalize($periods, 'jsonhal', $contextBuilder->toArray());
        $daysJson = $this->normalizer->normalize($days, 'jsonhal', $contextBuilder->toArray());
        $categoriesJson = $this->normalizer->normalize($categories, 'jsonhal', $contextBuilder->toArray());
        $activityProgressLabelsJson = $this->normalizer->normalize($activityProgressLabels, 'jsonhal', $contextBuilder->toArray());
        $activitiesJson = $this->normalizer->normalize($activities, 'jsonhal', $contextBuilder->toArray());
        $activityResponsiblesJson = $this->normalizer->normalize($activityResponsibles, 'jsonhal', $contextBuilder->toArray());
        $scheduleEntriesJson = $this->normalizer->normalize($scheduleEntries, 'jsonhal', $contextBuilder->toArray());

        return new \Symfony\Component\HttpFoundation\JsonResponse([
            'camp' => array_merge_recursive(
                $campJson,
                $this->createLinkCollection('periods', $periodsJson),
                $this->createLinkCollection('categories', $categoriesJson),
                $this->createLinkCollection('campCollaborations', $campCollaborationsJons),
            ),
            'campCollaborations' => $campCollaborationsJons,
            'users' => $usersJson,
            'periods' => array_map(
                fn ($p) => array_merge_recursive(
                    $p,
                    $this->createLinkCollectionFiltered($p, 'days', $daysJson, 'period'),
                ),
                $periodsJson
            ),
            'days' => $daysJson,
            'categories' => $categoriesJson,
            'activityProgressLabels' => $activityProgressLabelsJson,
            'activities' => array_map(
                fn ($a) => array_merge_recursive(
                    $a,
                    $this->createLinkCollectionFiltered($a, 'activityResponsibles', $activityResponsiblesJson, 'activity')
                ),
                $activitiesJson
            ),
            'activityResponsibles' => $activityResponsiblesJson,
            'scheduleEntries' => $scheduleEntriesJson,
        ]);
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
            if ($child['_links'][$parentLink]['href'] == $item['_links']['self']['href']) {
                $children[] = $child;
            }
        }

        return $this->createLinkCollection($listName, $children);
    }
}
