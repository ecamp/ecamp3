<?php
namespace EcampDB\Fixtures\Test;

use EcampCore\Entity\Event;
use EcampCore\Entity\EventInstance;
use EcampDB\Fixtures\Prod\CampTypeFixture;
use EcampDB\Fixtures\Prod\EventTypeFixture;

use EcampCore\Entity\Camp;
use EcampCore\Entity\CampType;
use EcampCore\Entity\Day;
use EcampCore\Entity\EventCategory;
use EcampCore\Entity\EventType;
use EcampCore\Entity\Group;
use EcampCore\Entity\Period;
use EcampCore\Entity\User;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use EcampDB\Fixtures\Prod\GroupFixture;

class Camps extends AbstractFixture implements OrderedFixtureInterface
{
    const PFILA = 'camp-pfila';
    const SOMMERLAGER = 'camp-sommerlager';
    const LEITERKURS = 'camp-leiterkurs';


    public function load(ObjectManager $manager)
    {
        $this->load_($manager, array(
            array(
                'name' => 'PfiLa',
                'title' => 'PfiLa',
                'motto' => 'PfiLa',
                'campType' => CampTypeFixture::JUGENDSPORT,
                'creator' => UserFixture::JOHN,
                'owner-user' => UserFixture::JOHN,

                'periods' => array(
                    array(
                        'desc' => 'Default Period',
                        'start' => new \DateTime('2018-05-04'),
                        'length' => 4,
                        'reference' => self::PFILA . ':P1'
                    )
                ),

                'categories' => array(
                    array(
                        'name' => 'Lagersport',
                        'short' => 'LS',
                        'eventType' => EventTypeFixture::LAGERSPORT,
                        'reference' => self::PFILA . ':ls'
                    ),
                    array(
                        'name' => 'Lageraktivität',
                        'short' => 'LA',
                        'eventType' => EventTypeFixture::LAGERAKTIVITAET,
                        'reference' => self::PFILA . ':la'
                    ),
                    array(
                        'name' => 'Lagerprogramm',
                        'short' => 'LP',
                        'eventType' => EventTypeFixture::LAGERPROGRAMM,
                        'reference' => self::PFILA . ':lp'
                    )
                ),

                'events' => array(
                    array(
                        'title' => 'Olympiade',
                        'category' => self::PFILA . ':ls',
                        'instances' => array(
                            array(
                                'period' => self::PFILA . ':P1',
                                'minOffsetStart' => 840,
                                'duration' => 240
                            )
                        )
                    ),
                ),

                'reference' => self::PFILA
            ),
            array(
                'name' => 'SoLa',
                'title' => 'SoLa',
                'motto' => 'SoLa',
                'campType' => CampTypeFixture::JUGENDSPORT,
                'creator' => UserFixture::TIFFANY,
                'owner-user' => UserFixture::TIFFANY,

                'periods' => array(
                    array(
                        'desc' => 'Default Period',
                        'start' => new \DateTime('2018-05-04'),
                        'length' => 4,
                        'reference' => self::SOMMERLAGER . ':P1'
                    )
                ),

                'categories' => array(
                    array(
                        'name' => 'Lagersport',
                        'short' => 'LS',
                        'eventType' => EventTypeFixture::LAGERSPORT,
                        'reference' => self::SOMMERLAGER . ':ls'
                    ),
                    array(
                        'name' => 'Lageraktivität',
                        'short' => 'LA',
                        'eventType' => EventTypeFixture::LAGERAKTIVITAET,
                        'reference' => self::SOMMERLAGER . ':la'
                    ),
                    array(
                        'name' => 'Lagerprogramm',
                        'short' => 'LP',
                        'eventType' => EventTypeFixture::LAGERPROGRAMM,
                        'reference' => self::SOMMERLAGER . ':lp'
                    )
                ),

                'events' => array(
                    array(
                        'title' => 'Olympiade',
                        'category' => self::SOMMERLAGER . ':ls',
                        'instances' => array(
                            array(
                                'period' => self::SOMMERLAGER . ':P1',
                                'minOffsetStart' => 840,
                                'duration' => 240
                            )
                        )
                    ),
                ),

                'reference' => self::SOMMERLAGER
            ),
            array(
                'name' => 'LU 222-18',
                'title' => 'LU 222-18',
                'motto' => 'LU 222-18',
                'campType' => CampTypeFixture::JUGENDSPORT,
                'creator' => UserFixture::TIFFANY,
                'owner-user' => UserFixture::TIFFANY,

                'periods' => array(
                    array(
                        'desc' => 'Default Period',
                        'start' => new \DateTime('2018-07-04'),
                        'length' => 4,
                        'reference' => self::LEITERKURS . ':P1'
                    )
                ),

                'categories' => array(
                    array(
                        'name' => 'Lagersport',
                        'short' => 'LS',
                        'eventType' => EventTypeFixture::LAGERSPORT,
                        'reference' => self::LEITERKURS . ':ls'
                    ),
                    array(
                        'name' => 'Lageraktivität',
                        'short' => 'LA',
                        'eventType' => EventTypeFixture::LAGERAKTIVITAET,
                        'reference' => self::LEITERKURS . ':la'
                    ),
                    array(
                        'name' => 'Lagerprogramm',
                        'short' => 'LP',
                        'eventType' => EventTypeFixture::LAGERPROGRAMM,
                        'reference' => self::LEITERKURS . ':lp'
                    )
                ),

                'events' => array(
                    array(
                        'title' => 'Olympiade',
                        'category' => self::LEITERKURS . ':ls',
                        'instances' => array(
                            array(
                                'period' => self::LEITERKURS . ':P1',
                                'minOffsetStart' => 840,
                                'duration' => 240
                            )
                        )
                    ),
                ),

                'reference' => self::LEITERKURS
            ),
        ));

        for($i = 0; $i < 10; $i++){
            $name = 'LU 11' . $i . '-17';
            $ref = self::LEITERKURS . UserFixture::TIFFANY . $i;

            $this->load_($manager, array(
                array(
                    'name' => $name,
                    'title' => $name,
                    'motto' => $name,
                    'campType' => CampTypeFixture::JUGENDSPORT,
                    'creator' => UserFixture::TIFFANY,
                    'owner-user' => UserFixture::TIFFANY,

                    'periods' => array(
                        array(
                            'desc' => 'Default Period',
                            'start' => new \DateTime('201' . $i . '-07-1' . $i),
                            'length' => 4,
                            'reference' => $ref . ':P1'
                        )
                    ),

                    'categories' => array(
                        array(
                            'name' => 'Lagersport',
                            'short' => 'LS',
                            'eventType' => EventTypeFixture::LAGERSPORT,
                            'reference' => $ref . ':ls'
                        ),
                        array(
                            'name' => 'Lageraktivität',
                            'short' => 'LA',
                            'eventType' => EventTypeFixture::LAGERAKTIVITAET,
                            'reference' => $ref . ':la'
                        ),
                        array(
                            'name' => 'Lagerprogramm',
                            'short' => 'LP',
                            'eventType' => EventTypeFixture::LAGERPROGRAMM,
                            'reference' => $ref . ':lp'
                        )
                    ),

                    'events' => array(
                        array(
                            'title' => 'Olympiade',
                            'category' => $ref . ':ls',
                            'instances' => array(
                                array(
                                    'period' => $ref . ':P1',
                                    'minOffsetStart' => 840,
                                    'duration' => 240
                                )
                            )
                        ),
                    ),

                    'reference' => $ref
                )
            ));
        }

        for($i = 0; $i < 10; $i++){
            $name = 'LU 21' . $i . '-18';
            $ref = self::LEITERKURS . GroupFixture::PBS . $i;

            $this->load_($manager, array(
                array(
                    'name' => $name,
                    'title' => $name,
                    'motto' => $name,
                    'campType' => CampTypeFixture::JUGENDSPORT,
                    'creator' => UserFixture::JOHN,
                    'owner-user' => GroupFixture::PBS,

                    'periods' => array(
                        array(
                            'desc' => 'Default Period',
                            'start' => new \DateTime('201' . $i . '-07-1' . $i),
                            'length' => 4,
                            'reference' => $ref . ':P1'
                        )
                    ),

                    'categories' => array(
                        array(
                            'name' => 'Lagersport',
                            'short' => 'LS',
                            'eventType' => EventTypeFixture::LAGERSPORT,
                            'reference' => $ref . ':ls'
                        ),
                        array(
                            'name' => 'Lageraktivität',
                            'short' => 'LA',
                            'eventType' => EventTypeFixture::LAGERAKTIVITAET,
                            'reference' => $ref . ':la'
                        ),
                        array(
                            'name' => 'Lagerprogramm',
                            'short' => 'LP',
                            'eventType' => EventTypeFixture::LAGERPROGRAMM,
                            'reference' => $ref . ':lp'
                        )
                    ),

                    'events' => array(
                        array(
                            'title' => 'Olympiade',
                            'category' => $ref . ':ls',
                            'instances' => array(
                                array(
                                    'period' => $ref . ':P1',
                                    'minOffsetStart' => 840,
                                    'duration' => 240
                                )
                            )
                        ),
                    ),

                    'reference' => $ref
                )
            ));
        }
    }

    private function load_(ObjectManager $manager, array $config)
    {
        $campRepo = $manager->getRepository('EcampCore\Entity\Camp');

        foreach ($config as $campConfig) {
            $name = $campConfig['name'];
            $title = $campConfig['title'];
            $motto = $campConfig['motto'];
            $reference = $campConfig['reference'];

            /** @var CampType $campType */
            $campType = $this->getReference($campConfig['campType']);

            /** @var User $creator */
            $creator = $this->getReference($campConfig['creator']);

            $owner = null;

            if(array_key_exists('owner-user', $campConfig)){
                /** @var User $owner */
                $owner = $this->getReference($campConfig['owner-user']);
            }
            if(array_key_exists('owner-group', $campConfig)){
                /** @var Group $owner */
                $owner = $this->getReference($campConfig['owner-group']);
            }

            $periods = $campConfig['periods'];
            $categories = $campConfig['categories'];
            $events = $campConfig['events'];

            $camp = $campRepo->findOneBy(array('owner' => $owner, 'name' => $name));

            if($camp == null){
                $camp = new Camp();
                $camp->setOwner($owner);
                $camp->setName($name);
                $manager->persist($camp);
            }

            $camp->setCampType($campType);
            $camp->setCreator($creator);
            $camp->setTitle($title);
            $camp->setMotto($motto);

            $this->loadPeriod_($manager, $camp, $periods);
            $this->loadCategories_($manager, $camp, $categories);
            $this->loadEvents_($manager, $camp, $events);

            $this->addReference($reference, $camp);
        }

        $manager->flush();
    }

    private function loadPeriod_(ObjectManager $manager, Camp $camp, array $config)
    {
        $periodRepo = $manager->getRepository('EcampCore\Entity\Period');

        foreach($config as $periodConfig){
            $desc = $periodConfig['desc'];
            $start = $periodConfig['start'];
            $length = $periodConfig['length'];
            $reference = $periodConfig['reference'];

            $period = $periodRepo->findOneBy(array(
                'camp' => $camp,
                'description' => $desc
            ));

            if($period == null){
                $period = new Period($camp);
                $period->setDescription($desc);
                $manager->persist($period);
            }

            $period->setStart($start);

            for($i = $period->getNumberOfDays(); $i <= $length; $i++){
                $day = new Day($period, $i);
                $manager->persist($day);
            }

            $this->addReference($reference, $period);
        }
    }

    public function loadCategories_(ObjectManager $manager, Camp $camp, array $config)
    {
        $categoryRepo = $manager->getRepository('EcampCore\Entity\EventCategory');

        foreach ($config as $categoryConfig) {
            $name = $categoryConfig['name'];
            $short = $categoryConfig['short'];
            $reference = $categoryConfig['reference'];

            /** @var EventType $eventType */
            $eventType = $this->getReference($categoryConfig['eventType']);

            $category = $categoryRepo->findOneBy(array(
                'camp' => $camp,
                'name' => $name
            ));

            if($category == null){
                $category = new EventCategory($camp, $eventType);
                $category->setName($name);
                $manager->persist($category);
            }

            $category->setShort($short);

            if(array_key_exists('color', $categoryConfig)){
                $category->setColor($categoryConfig['color']);
            }
            if(array_key_exists('numberingStyle', $categoryConfig)){
                $category->setNumberingStyle($categoryConfig['numberingStyle']);
            }

            $this->addReference($reference, $category);
        }
    }

    public function loadEvents_(ObjectManager $manager, Camp $camp, array $config)
    {
        $eventRepo = $manager->getRepository('EcampCore\Entity\Event');
        $eventInstanceRepo = $manager->getRepository('EcampCore\Entity\EventInstance');


        foreach ($config as $eventConfig) {
            /** @var EventCategory $eventCategory */
            $eventCategory = $this->getReference($eventConfig['category']);
            $title = $eventConfig['title'];
            $instances = $eventConfig['instances'];

            /** @var Event $event */
            $event = $eventRepo->findOneBy(array(
                'camp' => $camp,
                'title' => $title
            ));

            if($event == null){
                $event = new Event($camp, $eventCategory);
                $event->setTitle($title);
                $manager->persist($event);
            } else {
                $event->setEventCategory($eventCategory);
            }


            foreach ($instances as $instanceConfig) {
                /** @var Period $period */
                $period = $this->getReference($instanceConfig['period']);
                $minOffsetStart = $instanceConfig['minOffsetStart'];
                $duration = $instanceConfig['duration'];

                $instance = $eventInstanceRepo->findOneBy(array(
                    'event' => $event,
                    'period' => $period,
                    'minOffsetStart' => $minOffsetStart
                ));

                if($instance == null){
                    $instance = new EventInstance($event);
                    $instance->setPeriod($period);
                    $instance->setMinOffsetStart($minOffsetStart);
                    $manager->persist($instance);
                }

                $instance->setDuration($duration);

                if(array_key_exists('leftOffset', $instanceConfig)){
                    $instance->setLeftOffset($instanceConfig['leftOffset']);
                }
                if(array_key_exists('width', $instanceConfig)){
                    $instance->setWidth($instanceConfig['width']);
                }
            }

        }
    }

    public function getOrder()
    {
        return 110;
    }
}
