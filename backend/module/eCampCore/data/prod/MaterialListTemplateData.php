<?php

namespace eCamp\CoreData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\CampTemplate;
use eCamp\Core\Entity\MaterialListTemplate;

class MaterialListTemplateData extends AbstractFixture implements DependentFixtureInterface {
    public static $MIGROS = 'MIGROS';
    public static $COOP = 'COOP';

    private ObjectManager $manager;

    public function load(ObjectManager $manager): void {
        $this->manager = $manager;

        $repository = $manager->getRepository(MaterialListTemplate::class);

        /** @var MaterialListTemplate $migros */
        $migros = $repository->findOneBy(['name' => 'Migros']);
        if (null == $migros) {
            $migros = new MaterialListTemplate();
            $migros->setName('Migros');
            $manager->persist($migros);
        }
        $this->addReference(self::$MIGROS, $migros);

        /** @var MaterialListTemplate $migros */
        $coop = $repository->findOneBy(['name' => 'Coop']);
        if (null == $coop) {
            $coop = new MaterialListTemplate();
            $coop->setName('Coop');
            $manager->persist($coop);
        }
        $this->addReference(self::$COOP, $coop);

        /** @var CampTemplate $pbsJsKids */
        $pbsJsKids = $this->getReference(CampTemplateData::$PBS_JS_KIDS);
        if (!$pbsJsKids->getMaterialListTemplates()->contains($migros)) {
            $pbsJsKids->addMaterialListTemplate($migros);
        }
        if (!$pbsJsKids->getMaterialListTemplates()->contains($coop)) {
            $pbsJsKids->addMaterialListTemplate($coop);
        }

        /** @var CampTemplate $pbsJsTeen */
        $pbsJsTeen = $this->getReference(CampTemplateData::$PBS_JS_TEEN);
        if (!$pbsJsTeen->getMaterialListTemplates()->contains($migros)) {
            $pbsJsTeen->addMaterialListTemplate($migros);
        }
        if (!$pbsJsTeen->getMaterialListTemplates()->contains($coop)) {
            $pbsJsTeen->addMaterialListTemplate($coop);
        }

        $manager->flush();
    }

    public function getDependencies() {
        return [CampTemplateData::class];
    }
}
