<?php
namespace EcampDB\Fixtures\Test;

use EcampMaterial\Entity\MaterialList;

use EcampCore\Entity\Event;

use EcampCore\Entity\EventInstance;

use EcampCore\Entity\EventCategory;

use EcampCore\Entity\Day;

use EcampCore\Entity\Period;
use EcampCore\Entity\Camp;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class Camps extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $camp1 = new Camp();
        $manager->persist($camp1);
        $this->addReference('camp1', $camp1);

        $period1 = new Period($camp1);
        $period1->setStart(new \DateTime );
        $period1->setDescription("Bundeslager");
        $manager->persist($period1);

        $day = new Day($period1, 0); $manager->persist($day);
        $day = new Day($period1, 1); $manager->persist($day);
        $day = new Day($period1, 2); $manager->persist($day);

        $camp1->setOwner($this->getReference('group-pbs'));
        $camp1->setCreator($this->getReference('user1'));
        $camp1->setCampType($this->getReference('camptype-jugendsport'));

        $camp1->setName("Bundeslager");
        $camp1->setTitle("Bundeslager");
        $camp1->setMotto("Bundeslager");

        $category = new EventCategory($camp1, $this->getReference('eventtype-lagersport'));
        $category->setName("Lagersport");
        $category->setShort("LS");
        $manager->persist($category);
        $this->addReference('category-lagersport', $category);

        $event1 = new Event($camp1, $category);
        $event1->setTitle("Morgenturnen");
        $manager->persist($event1);

        $instance1 = new EventInstance($event1);
        $instance1->setPeriod($period1);
        $instance1->setDuration(30);
        $instance1->setOffset(8*60);
        $manager->persist($instance1);

        $materiallist = new MaterialList($camp1);
        $materiallist->setName("Einkaufsliste");
        $manager->persist($materiallist);

        $manager->flush();
    }

    public function getOrder()
    {
        return 20;
    }
}
