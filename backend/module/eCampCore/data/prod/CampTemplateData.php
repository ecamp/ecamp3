<?php

namespace eCamp\CoreData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\CampTemplate;

class CampTemplateData extends AbstractFixture {
    public static $PBS_JS_KIDS = CampTemplate::class.':PBS_JS_KIDS';
    public static $PBS_JS_TEEN = CampTemplate::class.':PBS_JS_TEEN';

    public function load(ObjectManager $manager): void {
        $repository = $manager->getRepository(CampTemplate::class);

        /** @var CampTemplate $campTemplate */
        $campTemplate = $repository->findOneBy(['name' => 'J+S Kids']);
        if (null == $campTemplate) {
            $campTemplate = new CampTemplate();
            $campTemplate->setName('J+S Kids');

            $manager->persist($campTemplate);
        }
        $this->addReference(self::$PBS_JS_KIDS, $campTemplate);

        /** @var CampTemplate $campTemplate */
        $campTemplate = $repository->findOneBy(['name' => 'J+S Teen']);
        if (null == $campTemplate) {
            $campTemplate = new CampTemplate();
            $campTemplate->setName('J+S Teen');

            $manager->persist($campTemplate);
        }
        $this->addReference(self::$PBS_JS_TEEN, $campTemplate);

        $manager->flush();
    }
}
