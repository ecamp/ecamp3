<?php

namespace eCamp\CoreData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\CampTemplate;
use eCamp\Core\Entity\CategoryContentTypeTemplate;
use eCamp\Core\Entity\CategoryTemplate;
use eCamp\Core\Entity\ContentType;

class CategoryTemplateData extends AbstractFixture implements DependentFixtureInterface {
    public static $PBS_JS_KIDS_LAGERSPORT = 'PBS_JS_KIDS_LAGERSPORT';
    public static $PBS_JS_KIDS_LAGERAKTIVITAET = 'PBS_JS_KIDS_LAGERAKTIVITAET';
    public static $PBS_JS_TEEN_LAGERSPORT = 'PBS_JS_TEEN_LAGERSPORT';
    public static $PBS_JS_TEEN_LAGERAKTIVITAET = 'PBS_JS_TEEN_LAGERAKTIVITAET';

    public function load(ObjectManager $manager): void {
        $repository = $manager->getRepository(CategoryTemplate::class);

        /** @var CampTemplate $pbsJsKids */
        $pbsJsKids = $this->getReference(CampTemplateData::$PBS_JS_KIDS);

        $lagersport = $repository->findOneBy(['name' => 'Lagersport', 'campTemplate' => $pbsJsKids]);
        if (null == $lagersport) {
            $lagersport = new CategoryTemplate();
            $lagersport->setShort('LS');
            $lagersport->setName('Lagersport');
            $lagersport->setColor('#4CAF50');
            $lagersport->setNumberingStyle('1');
            $pbsJsKids->addCategoryTemplate($lagersport);
            $manager->persist($lagersport);

            // add allowed content types
            $this->addContentType($manager, $lagersport, $this->getReference(ContentTypeData::$STORYBOARD));
            $this->addContentType($manager, $lagersport, $this->getReference(ContentTypeData::$STORYCONTEXT));
            $this->addContentType($manager, $lagersport, $this->getReference(ContentTypeData::$SAFETYCONCEPT));
            $this->addContentType($manager, $lagersport, $this->getReference(ContentTypeData::$NOTES));
            $this->addContentType($manager, $lagersport, $this->getReference(ContentTypeData::$MATERIAL));
        }
        $this->addReference(self::$PBS_JS_KIDS_LAGERSPORT, $lagersport);

        $lageraktivitaet = $repository->findOneBy(['name' => 'Lageraktivität', 'campTemplate' => $pbsJsKids]);
        if (null == $lageraktivitaet) {
            $lageraktivitaet = new CategoryTemplate();
            $lageraktivitaet->setShort('LA');
            $lageraktivitaet->setName('Lageraktivität');
            $lageraktivitaet->setColor('#FF9800');
            $lageraktivitaet->setNumberingStyle('A');
            $pbsJsKids->addCategoryTemplate($lageraktivitaet);
            $manager->persist($lageraktivitaet);

            // add allowed content types
            //$this->addContentType($activityType, $this->getReference(ContentTypeData::$STORYBOARD));
            $this->addContentType($manager, $lageraktivitaet, $this->getReference(ContentTypeData::$STORYCONTEXT));
            $this->addContentType($manager, $lageraktivitaet, $this->getReference(ContentTypeData::$NOTES));
            $this->addContentType($manager, $lageraktivitaet, $this->getReference(ContentTypeData::$MATERIAL));
            $this->addContentType($manager, $lageraktivitaet, $this->getReference(ContentTypeData::$LATHEMATICAREA));
        }
        $this->addReference(self::$PBS_JS_KIDS_LAGERAKTIVITAET, $lageraktivitaet);

        /** @var CampTemplate $pbsJsTeen */
        $pbsJsTeen = $this->getReference(CampTemplateData::$PBS_JS_TEEN);

        $lagersport = $repository->findOneBy(['name' => 'Lagersport', 'campTemplate' => $pbsJsTeen]);
        if (null == $lagersport) {
            $lagersport = new CategoryTemplate();
            $lagersport->setShort('LS');
            $lagersport->setName('Lagersport');
            $lagersport->setColor('#4CAF50');
            $lagersport->setNumberingStyle('1');
            $pbsJsTeen->addCategoryTemplate($lagersport);
            $manager->persist($lagersport);

            // add allowed content types
            $this->addContentType($manager, $lagersport, $this->getReference(ContentTypeData::$STORYBOARD));
            $this->addContentType($manager, $lagersport, $this->getReference(ContentTypeData::$STORYCONTEXT));
            $this->addContentType($manager, $lagersport, $this->getReference(ContentTypeData::$SAFETYCONCEPT));
            $this->addContentType($manager, $lagersport, $this->getReference(ContentTypeData::$NOTES));
            $this->addContentType($manager, $lagersport, $this->getReference(ContentTypeData::$MATERIAL));
        }
        $this->addReference(self::$PBS_JS_TEEN_LAGERSPORT, $lagersport);

        $lageraktivitaet = $repository->findOneBy(['name' => 'Lageraktivität', 'campTemplate' => $pbsJsTeen]);
        if (null == $lageraktivitaet) {
            $lageraktivitaet = new CategoryTemplate();
            $lageraktivitaet->setShort('LA');
            $lageraktivitaet->setName('Lageraktivität');
            $lageraktivitaet->setColor('#FF9800');
            $lageraktivitaet->setNumberingStyle('A');
            $pbsJsTeen->addCategoryTemplate($lageraktivitaet);
            $manager->persist($lageraktivitaet);

            // add allowed content types
            //$this->addContentType($activityType, $this->getReference(ContentTypeData::$STORYBOARD));
            $this->addContentType($manager, $lageraktivitaet, $this->getReference(ContentTypeData::$STORYCONTEXT));
            $this->addContentType($manager, $lageraktivitaet, $this->getReference(ContentTypeData::$NOTES));
            $this->addContentType($manager, $lageraktivitaet, $this->getReference(ContentTypeData::$MATERIAL));
            $this->addContentType($manager, $lageraktivitaet, $this->getReference(ContentTypeData::$LATHEMATICAREA));
        }
        $this->addReference(self::$PBS_JS_TEEN_LAGERAKTIVITAET, $lageraktivitaet);

        $manager->flush();
    }

    public function getDependencies() {
        return [CampTemplateData::class, ContentTypeData::class];
    }

    private function addContentType(ObjectManager $manager, CategoryTemplate $categoryTemplate, ContentType $contentType): CategoryContentTypeTemplate {
        $categoryContentTypeTemplate = new CategoryContentTypeTemplate();
        $categoryContentTypeTemplate->setContentType($contentType);
        $categoryTemplate->addCategoryContentTypeTemplate($categoryContentTypeTemplate);
        $manager->persist($categoryContentTypeTemplate);

        return $categoryContentTypeTemplate;
    }
}
