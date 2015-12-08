<?php

namespace EcampDB\Fixtures\Test;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use EcampCore\Entity\Medium;

class MediumFixture extends AbstractFixture implements OrderedFixtureInterface
{
    const MEDIUM_WEB = 'medium-web';
    const MEDIUM_PRINT = 'medium-print';
    const MEDIUM_MOBILE = 'medium-mobile';


    public function load(ObjectManager $manager)
    {
        $repository = $manager->getRepository('EcampCore\Entity\Medium');

        $web = $repository->findOneBy(array('name' => Medium::MEDIUM_WEB));
        $print = $repository->findOneBy(array('name' => Medium::MEDIUM_PRINT));
        $mobile = $repository->findOneBy(array('name' => Medium::MEDIUM_MOBILE));

        if($web == null){
            $web = new Medium(Medium::MEDIUM_WEB, true);
            $manager->persist($web);
        }

        if($print == null){
            $print = new Medium(Medium::MEDIUM_PRINT, false);
            $manager->persist($print);
        }

        if($mobile == null){
            $mobile = new Medium(Medium::MEDIUM_MOBILE, false);
            $manager->persist($mobile);
        }


        $manager->flush();

        $this->addReference(Medium::MEDIUM_WEB, $web);
        $this->addReference(Medium::MEDIUM_PRINT, $print);
        $this->addReference(Medium::MEDIUM_MOBILE, $mobile);
    }

    public function getOrder()
    {
        return 1;
    }

}