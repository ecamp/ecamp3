<?php

namespace EcampDB\Fixtures\Prod;

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
        $this->load_($manager, array(
            array(
                'name' => Medium::MEDIUM_WEB,
                'default' => true,
                'reference' => self::MEDIUM_WEB
            ),
            array(
                'name' => Medium::MEDIUM_PRINT,
                'default' => false,
                'reference' => self::MEDIUM_PRINT
            ),
            array(
                'name' => Medium::MEDIUM_MOBILE,
                'default' => false,
                'reference' => self::MEDIUM_MOBILE
            ),
        ));
    }

    private function load_(ObjectManager $manager, array $config)
    {
        $mediumRepo = $manager->getRepository('EcampCore\Entity\Medium');

        foreach($config as $mediumConfig){
            $name = $mediumConfig['name'];
            $default = $mediumConfig['default'];
            $reference = $mediumConfig['reference'];

            /** @var Medium $medium */
            $medium = $mediumRepo->findOneBy(array('name' => $name));

            if($medium == null){
                $medium = new Medium($name, $default);
                $manager->persist($medium);
            } else {
                $medium->setDefault($default);
            }

            $this->addReference($reference, $medium);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }

}