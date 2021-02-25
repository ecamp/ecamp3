<?php

namespace eCamp\CoreTest\Data;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use eCamp\Core\ContentType\ContentTypeStrategyProvider;
use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\ContentNode;
use eCamp\Core\Entity\ContentType;
use eCamp\Core\Entity\MaterialItem;
use eCamp\Core\Entity\MaterialList;
use Interop\Container\ContainerInterface;

class MaterialItemTestData extends AbstractFixture implements DependentFixtureInterface {
    public static $MATERIALITEM1 = MaterialItem::class.':MATERIALITEM1';
    public static $CONTENTNODE1 = ContentNode::class.':CONTENTNODE1';

    protected $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function load(ObjectManager $manager): void {
        $contentTypeStrategyProvider = new ContentTypeStrategyProvider($this->container);

        /** @var Activity $activity */
        $activity = $this->getReference(ActivityTestData::$ACTIVITY1);

        /** @var ContentType $contentType */
        $contentType = $this->getReference(ContentTypeTestData::$TYPE_MATERIAL);

        /** @var MaterialList $materialList */
        $materialList = $this->getReference(MaterialListTestData::$MATERIALLIST1);

        $contentNode = new ContentNode();
        $contentNode->setActivity($activity);
        $contentNode->setContentType($contentType);
        $contentNode->setContentTypeStrategyProvider($contentTypeStrategyProvider);

        $materialItem = new MaterialItem();
        $materialItem->setQuantity(2);
        $materialItem->setUnit('kg');
        $materialItem->setArticle('art');
        $materialItem->setContentNode($contentNode);
        $materialList->addMaterialItem($materialItem);

        $manager->persist($contentNode);
        $manager->persist($materialItem);
        $manager->flush();

        $this->addReference(self::$CONTENTNODE1, $contentNode);
        $this->addReference(self::$MATERIALITEM1, $materialItem);
    }

    public function getDependencies() {
        return [ActivityTestData::class, MaterialListTestData::class, ContentTypeTestData::class];
    }
}
