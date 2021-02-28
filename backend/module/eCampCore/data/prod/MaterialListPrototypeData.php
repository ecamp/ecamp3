<?php

namespace eCamp\CoreData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\MaterialList;

class MaterialListPrototypeData extends AbstractFixture implements DependentFixtureInterface {
    public static $MIGROS = 'MIGROS';
    public static $COOP = 'COOP';

    public function load(ObjectManager $manager): void {
        $repository = $manager->getRepository(MaterialList::class);

        /** @var MaterialList $migros */
        $migros = $repository->findOneBy(['name' => 'Migros']);
        if (null == $migros) {
            $migros = new MaterialList();
            $migros->setName('Migros');
            $manager->persist($migros);
        }
        $this->addReference(self::$MIGROS, $migros);

        /** @var Camp $pbsJsKids */
        $pbsJsKids = $this->getReference(CampPrototypeData::$PBS_JS_KIDS);
        if (!$pbsJsKids->getMaterialLists()->contains($migros)) {
            $pbsJsKids->addMaterialList($migros);
        }

        /** @var MaterialList $coop */
        $coop = $repository->findOneBy(['name' => 'Coop']);
        if (null == $coop) {
            $coop = new MaterialList();
            $coop->setName('Coop');
            $manager->persist($coop);
        }
        $this->addReference(self::$COOP, $coop);

        /** @var Camp $pbsJsTeen */
        $pbsJsTeen = $this->getReference(CampPrototypeData::$PBS_JS_TEEN);
        if (!$pbsJsTeen->getMaterialLists()->contains($coop)) {
            $pbsJsTeen->addMaterialList($coop);
        }

        $manager->flush();
    }

    public function getDependencies() {
        return [CampPrototypeData::class];
    }
}
