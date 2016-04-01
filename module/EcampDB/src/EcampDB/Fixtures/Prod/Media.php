<?php

namespace EcampDB\Fixtures\Test;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use EcampCore\Entity\Medium;

class Media // extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $webMedium = new Medium('web', true);
        $printMedium = new Medium('print', false);

        $manager->persist($webMedium);
        $manager->persist($printMedium);
        $manager->flush();
    }

    public function getOrder()
    {
        return 10;
    }
}
