<?php

namespace eCamp\CoreData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use eCamp\Core\Entity\Medium;

class MediumData extends AbstractFixture
{
    public static $WEB = Medium::class . ':WEB';
    public static $MOBILE= Medium::class . ':MOBILE';
    public static $PRINT = Medium::class . ':PRINT';


    public function load(ObjectManager $manager) {
        $repository = $manager->getRepository(Medium::class);


        $medium = $repository->findOneBy([ 'name' => 'Web' ]);
        if ($medium == null) {
            $medium = new Medium();
            $medium->setName('Web');
            $medium->setDefault(true);
            $manager->persist($medium);
        }
        $this->addReference(self::$WEB, $medium);

        $medium = $repository->findOneBy([ 'name' => 'Mobile' ]);
        if ($medium == null) {
            $medium = new Medium();
            $medium->setName('Mobile');
            $medium->setDefault(false);
            $manager->persist($medium);
        }
        $this->addReference(self::$MOBILE, $medium);

        $medium = $repository->findOneBy([ 'name' => 'Print' ]);
        if ($medium == null) {
            $medium = new Medium();
            $medium->setName('Print');
            $medium->setDefault(false);
            $manager->persist($medium);
        }
        $this->addReference(self::$PRINT, $medium);


        $manager->flush();
    }
}