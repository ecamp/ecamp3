<?php

namespace eCamp\CoreData\Prod;

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

        /** @var Camp $pbsJsKids */
        $pbsJsKids = $this->getReference(CampPrototypeData::$PBS_JS_KIDS);
        /** @var Camp $pbsJsTeen */
        $pbsJsTeen = $this->getReference(CampPrototypeData::$PBS_JS_TEEN);

        /** @var MaterialList $migros */
        $migros = $repository->findOneBy(['name' => 'Migros', 'camp' => $pbsJsKids->getId()]);
        if (null == $migros) {
            $migros = new MaterialList();
            $migros->setName('Migros');
            $manager->persist($migros);

            $pbsJsKids->addMaterialList($migros);
        }
        $this->addReference(self::$MIGROS, $migros);

        /** @var MaterialList $coop */
        $coop = $repository->findOneBy(['name' => 'Coop', 'camp' => $pbsJsTeen->getId()]);
        if (null == $coop) {
            $coop = new MaterialList();
            $coop->setName('Coop');
            $manager->persist($coop);

            $pbsJsTeen->addMaterialList($coop);
        }
        $this->addReference(self::$COOP, $coop);

        $manager->flush();
    }

    public function getDependencies() {
        return [CampPrototypeData::class];
    }
}
