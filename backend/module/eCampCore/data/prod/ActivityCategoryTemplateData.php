<?php

namespace eCamp\CoreData;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\Entity\ActivityCategoryTemplate;
use eCamp\Core\Entity\CampTemplate;
use eCamp\Core\Entity\ContentType;
use eCamp\Core\Entity\ContentTypeConfigTemplate;

class ActivityCategoryTemplateData extends AbstractFixture implements DependentFixtureInterface {
    public static $PBS_JS_KIDS_LAGERSPORT = 'PBS_JS_KIDS_LAGERSPORT';
    public static $PBS_JS_KIDS_LAGERAKTIVITAET = 'PBS_JS_KIDS_LAGERAKTIVITAET';
    public static $PBS_JS_TEEN_LAGERSPORT = 'PBS_JS_TEEN_LAGERSPORT';
    public static $PBS_JS_TEEN_LAGERAKTIVITAET = 'PBS_JS_TEEN_LAGERAKTIVITAET';

    private ObjectManager $manager;

    public function load(ObjectManager $manager) {
        $this->manager = $manager;

        $repository = $manager->getRepository(ActivityCategoryTemplate::class);

        /** @var CampTemplate $pbsJsKids */
        $pbsJsKids = $this->getReference(CampTemplateData::$PBS_JS_KIDS);

        $lagersport = $repository->findOneBy(['name' => 'Lagersport', 'campTemplate' => $pbsJsKids]);
        if (null == $lagersport) {
            $lagersport = new ActivityCategoryTemplate();
            $lagersport->setShort('LS');
            $lagersport->setName('Lagersport');
            $lagersport->setColor('#4CAF50');
            $lagersport->setNumberingStyle('1');
            $pbsJsKids->addActivityCategoryTemplate($lagersport);
            $manager->persist($lagersport);

            // add allowed content types
            $this->addContentType($lagersport, $this->getReference(ContentTypeData::$STORYBOARD));
            $this->addContentType($lagersport, $this->getReference(ContentTypeData::$STORYCONTEXT));
            $this->addContentType($lagersport, $this->getReference(ContentTypeData::$SAFETYCONCEPT));
            $this->addContentType($lagersport, $this->getReference(ContentTypeData::$NOTES));
            $this->addContentType($lagersport, $this->getReference(ContentTypeData::$MATERIAL));
        }
        $this->addReference(self::$PBS_JS_KIDS_LAGERSPORT, $lagersport);

        $lageraktivitaet = $repository->findOneBy(['name' => 'Lageraktivität', 'campTemplate' => $pbsJsKids]);
        if (null == $lageraktivitaet) {
            $lageraktivitaet = new ActivityCategoryTemplate();
            $lageraktivitaet->setShort('LA');
            $lageraktivitaet->setName('Lageraktivität');
            $lageraktivitaet->setColor('#FF9800');
            $lageraktivitaet->setNumberingStyle('A');
            $pbsJsKids->addActivityCategoryTemplate($lageraktivitaet);
            $manager->persist($lageraktivitaet);

            // add allowed content types
            //$this->addContentType($activityType, $this->getReference(ContentTypeData::$STORYBOARD));
            $this->addContentType($lageraktivitaet, $this->getReference(ContentTypeData::$STORYCONTEXT));
            $this->addContentType($lageraktivitaet, $this->getReference(ContentTypeData::$NOTES));
            $this->addContentType($lageraktivitaet, $this->getReference(ContentTypeData::$MATERIAL));
        }
        $this->addReference(self::$PBS_JS_KIDS_LAGERAKTIVITAET, $lageraktivitaet);

        /** @var CampTemplate $pbsJsTeen */
        $pbsJsTeen = $this->getReference(CampTemplateData::$PBS_JS_TEEN);

        $lagersport = $repository->findOneBy(['name' => 'Lagersport', 'campTemplate' => $pbsJsTeen]);
        if (null == $lagersport) {
            $lagersport = new ActivityCategoryTemplate();
            $lagersport->setShort('LS');
            $lagersport->setName('Lagersport');
            $lagersport->setColor('#4CAF50');
            $lagersport->setNumberingStyle('1');
            $pbsJsTeen->addActivityCategoryTemplate($lagersport);
            $manager->persist($lagersport);

            // add allowed content types
            $this->addContentType($lagersport, $this->getReference(ContentTypeData::$STORYBOARD));
            $this->addContentType($lagersport, $this->getReference(ContentTypeData::$STORYCONTEXT));
            $this->addContentType($lagersport, $this->getReference(ContentTypeData::$SAFETYCONCEPT));
            $this->addContentType($lagersport, $this->getReference(ContentTypeData::$NOTES));
            $this->addContentType($lagersport, $this->getReference(ContentTypeData::$MATERIAL));
        }
        $this->addReference(self::$PBS_JS_TEEN_LAGERSPORT, $lagersport);

        $lageraktivitaet = $repository->findOneBy(['name' => 'Lageraktivität', 'campTemplate' => $pbsJsTeen]);
        if (null == $lageraktivitaet) {
            $lageraktivitaet = new ActivityCategoryTemplate();
            $lageraktivitaet->setShort('LA');
            $lageraktivitaet->setName('Lageraktivität');
            $lageraktivitaet->setColor('#FF9800');
            $lageraktivitaet->setNumberingStyle('A');
            $pbsJsTeen->addActivityCategoryTemplate($lageraktivitaet);
            $manager->persist($lageraktivitaet);

            // add allowed content types
            //$this->addContentType($activityType, $this->getReference(ContentTypeData::$STORYBOARD));
            $this->addContentType($lageraktivitaet, $this->getReference(ContentTypeData::$STORYCONTEXT));
            $this->addContentType($lageraktivitaet, $this->getReference(ContentTypeData::$NOTES));
            $this->addContentType($lageraktivitaet, $this->getReference(ContentTypeData::$MATERIAL));
        }
        $this->addReference(self::$PBS_JS_TEEN_LAGERAKTIVITAET, $lageraktivitaet);

        $manager->flush();
    }

    public function getDependencies() {
        return [CampTemplateData::class, ContentTypeData::class];
    }

    private function addContentType(ActivityCategoryTemplate $activityCategoryTemplate, ContentType $contentType) {
        $contentTypeConfigTemplate = new ContentTypeConfigTemplate();
        $contentTypeConfigTemplate->setContentType($contentType);
        $activityCategoryTemplate->addContentTypeConfigTemplate($contentTypeConfigTemplate);
        $this->manager->persist($contentTypeConfigTemplate);

        return $contentTypeConfigTemplate;
    }
}
