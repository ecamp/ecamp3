<?php

namespace eCamp\CoreData\Prod;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Category;
use eCamp\Core\Entity\ContentNode;
use eCamp\Core\Entity\ContentType;
use eCamp\Lib\Fixture\ContainerAwareInterface;
use eCamp\Lib\Fixture\ContainerAwareTrait;

class CategoryPrototypeData extends AbstractFixture implements DependentFixtureInterface, ContainerAwareInterface {
    use ContainerAwareTrait;

    public static $PBS_JS_KIDS_LAGERSPORT = 'PBS_JS_KIDS_LAGERSPORT';
    public static $PBS_JS_KIDS_LAGERAKTIVITAET = 'PBS_JS_KIDS_LAGERAKTIVITAET';
    public static $PBS_JS_TEEN_LAGERSPORT = 'PBS_JS_TEEN_LAGERSPORT';
    public static $PBS_JS_TEEN_LAGERAKTIVITAET = 'PBS_JS_TEEN_LAGERAKTIVITAET';

    public function load(ObjectManager $manager): void {
        $repository = $manager->getRepository(Category::class);

        /** @var Camp $pbsJsKids */
        $pbsJsKids = $this->getReference(CampPrototypeData::$PBS_JS_KIDS);

        $lagersport = $repository->findOneBy(['name' => 'Lagersport', 'camp' => $pbsJsKids]);
        if (null == $lagersport) {
            $lagersport = new Category();
            $lagersport->setShort('LS');
            $lagersport->setName('Lagersport');
            $lagersport->setColor('#4CAF50');
            $lagersport->setNumberingStyle('1');
            $pbsJsKids->addCategory($lagersport);
            $manager->persist($lagersport);

            // add recommended content types
            $this->addContentType($lagersport, $this->getReference(ContentTypeData::$STORYBOARD));
            $this->addContentType($lagersport, $this->getReference(ContentTypeData::$STORYCONTEXT));
            $this->addContentType($lagersport, $this->getReference(ContentTypeData::$SAFETYCONCEPT));
            $this->addContentType($lagersport, $this->getReference(ContentTypeData::$NOTES));
            $this->addContentType($lagersport, $this->getReference(ContentTypeData::$MATERIAL));
        }
        $this->addReference(self::$PBS_JS_KIDS_LAGERSPORT, $lagersport);

        $lageraktivitaet = $repository->findOneBy(['name' => 'Lageraktivität', 'camp' => $pbsJsKids]);
        if (null == $lageraktivitaet) {
            $lageraktivitaet = new Category();
            $lageraktivitaet->setShort('LA');
            $lageraktivitaet->setName('Lageraktivität');
            $lageraktivitaet->setColor('#FF9800');
            $lageraktivitaet->setNumberingStyle('A');
            $pbsJsKids->addCategory($lageraktivitaet);
            $manager->persist($lageraktivitaet);

            // add recommended content types
            //$this->addContentType($activityType, $this->getReference(ContentTypeData::$STORYBOARD));
            $this->addContentType($lageraktivitaet, $this->getReference(ContentTypeData::$STORYCONTEXT));
            $this->addContentType($lageraktivitaet, $this->getReference(ContentTypeData::$NOTES));
            $this->addContentType($lageraktivitaet, $this->getReference(ContentTypeData::$MATERIAL));
            $this->addContentType($lageraktivitaet, $this->getReference(ContentTypeData::$LATHEMATICAREA));
        }
        $this->addReference(self::$PBS_JS_KIDS_LAGERAKTIVITAET, $lageraktivitaet);

        /** @var Camp $pbsJsTeen */
        $pbsJsTeen = $this->getReference(CampPrototypeData::$PBS_JS_TEEN);

        $lagersport = $repository->findOneBy(['name' => 'Lagersport', 'camp' => $pbsJsTeen]);
        if (null == $lagersport) {
            $lagersport = new Category();
            $lagersport->setShort('LS');
            $lagersport->setName('Lagersport');
            $lagersport->setColor('#4CAF50');
            $lagersport->setNumberingStyle('1');
            $pbsJsTeen->addCategory($lagersport);
            $manager->persist($lagersport);

            // add recommended content types
            $this->addContentType($lagersport, $this->getReference(ContentTypeData::$STORYBOARD));
            $this->addContentType($lagersport, $this->getReference(ContentTypeData::$STORYCONTEXT));
            $this->addContentType($lagersport, $this->getReference(ContentTypeData::$SAFETYCONCEPT));
            $this->addContentType($lagersport, $this->getReference(ContentTypeData::$NOTES));
            $this->addContentType($lagersport, $this->getReference(ContentTypeData::$MATERIAL));

            $lagersport->setRootContentNode($this->createInitialRootContentNode($manager));
        }
        $this->addReference(self::$PBS_JS_TEEN_LAGERSPORT, $lagersport);

        $lageraktivitaet = $repository->findOneBy(['name' => 'Lageraktivität', 'camp' => $pbsJsTeen]);
        if (null == $lageraktivitaet) {
            $lageraktivitaet = new Category();
            $lageraktivitaet->setShort('LA');
            $lageraktivitaet->setName('Lageraktivität');
            $lageraktivitaet->setColor('#FF9800');
            $lageraktivitaet->setNumberingStyle('A');
            $pbsJsTeen->addCategory($lageraktivitaet);
            $manager->persist($lageraktivitaet);

            // add recommended content types
            //$this->addContentType($activityType, $this->getReference(ContentTypeData::$STORYBOARD));
            $this->addContentType($lageraktivitaet, $this->getReference(ContentTypeData::$STORYCONTEXT));
            $this->addContentType($lageraktivitaet, $this->getReference(ContentTypeData::$NOTES));
            $this->addContentType($lageraktivitaet, $this->getReference(ContentTypeData::$MATERIAL));
            $this->addContentType($lageraktivitaet, $this->getReference(ContentTypeData::$LATHEMATICAREA));

            $lageraktivitaet->setRootContentNode($this->createInitialRootContentNode($manager));
        }
        $this->addReference(self::$PBS_JS_TEEN_LAGERAKTIVITAET, $lageraktivitaet);

        $manager->flush();
    }

    public function getDependencies() {
        return [CampPrototypeData::class, ContentTypeData::class];
    }

    protected function addContentType(Category $category, ContentType $contentType) {
        $category->addPreferredContentType($contentType);
    }

    protected function createInitialRootContentNode(ObjectManager $manager) {
        $contentNode = new ContentNode();
        /** @var ContentType $columnLayout */
        $columnLayout = $this->getReference(ContentTypeData::$COLUMNLAYOUT);
        $contentNode->setContentType($columnLayout);
        $this->getContainer()->get($columnLayout->getStrategyClass())->contentNodeCreated($contentNode);
        $manager->persist($contentNode);

        return $contentNode;
    }
}
