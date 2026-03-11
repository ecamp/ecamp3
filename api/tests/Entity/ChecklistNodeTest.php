<?php

namespace App\Tests\Entity;

use App\Entity\Camp;
use App\Entity\CampRootContentNode;
use App\Entity\Checklist;
use App\Entity\ChecklistItem;
use App\Entity\ContentNode\ChecklistNode;
use App\Entity\ContentNode\ColumnLayout;
use App\Util\EntityMap;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ChecklistNodeTest extends TestCase {
    private Camp $camp;
    private Checklist $checklist;
    private ChecklistItem $itemPrototype1;
    private ChecklistItem $itemPrototype3;
    private ChecklistNode $checklistNodePrototype;
    private ChecklistNode $checklistNode;

    public function setUp(): void {
        $this->camp = new Camp();
        $this->checklist = new Checklist();
        $this->checklist->name = 'checklist1';
        $this->camp->addChecklist($this->checklist);

        $rootNode = new ColumnLayout();
        $campRootContentNode = new CampRootContentNode();
        $campRootContentNode->camp = $this->camp;
        $campRootContentNode->rootContentNode = $rootNode;
        $rootNode->campRootContentNodes->add($campRootContentNode);

        $this->itemPrototype1 = new ChecklistItem();
        $this->itemPrototype1->text = 'item1';
        $this->checklist->addChecklistItem($this->itemPrototype1);
        $itemPrototype2 = new ChecklistItem();
        $itemPrototype2->text = 'item2';
        $this->checklist->addChecklistItem($itemPrototype2);
        $this->itemPrototype3 = new ChecklistItem();
        $this->itemPrototype3->text = 'item3';
        $itemPrototype2->addChild($this->itemPrototype3);
        $this->checklist->addChecklistItem($this->itemPrototype3);

        $this->checklistNodePrototype = new ChecklistNode();
        $this->checklistNodePrototype->root = $rootNode;

        $this->checklistNode = new ChecklistNode();
    }

    public function testCopyFromPrototypeInSameCamp() {
        // given
        $this->checklistNodePrototype->addChecklistItem($this->itemPrototype1);
        $this->checklistNodePrototype->addChecklistItem($this->itemPrototype3);

        // when
        $this->checklistNode->copyFromPrototype($this->checklistNodePrototype, new EntityMap($this->camp));

        // then
        $this->assertCount(2, $this->checklistNode->getChecklistItems());
        $item1 = $this->checklistNode->getChecklistItems()[0];
        $item2 = $this->checklistNode->getChecklistItems()[1];
        $this->assertEquals($this->itemPrototype1->text, $item1->text);
        $this->assertEquals($this->itemPrototype3->text, $item2->text);
        $this->assertEquals($this->itemPrototype1->checklist->getCamp(), $item1->checklist->getCamp());
        $this->assertEquals($this->itemPrototype3->checklist->getCamp(), $item2->checklist->getCamp());
    }

    public function testCopyFromPrototypeAcrossCampsDoesNotAddFaultyConnections() {
        // given
        $this->checklistNodePrototype->addChecklistItem($this->itemPrototype1);
        $this->checklistNodePrototype->addChecklistItem($this->itemPrototype3);
        $targetCamp = new Camp();

        // when
        $this->checklistNode->copyFromPrototype($this->checklistNodePrototype, new EntityMap($targetCamp));

        // then
        $this->assertCount(0, $this->checklistNode->getChecklistItems());
    }

    public function testCopyFromPrototypeAcrossCampsSearchesForItemOfSameName() {
        // given
        $this->checklistNodePrototype->addChecklistItem($this->itemPrototype1);
        $this->checklistNodePrototype->addChecklistItem($this->itemPrototype3);
        $targetCamp = new Camp();

        $targetChecklist = new Checklist();
        $targetChecklist->name = 'target checklist';
        $targetCamp->addChecklist($targetChecklist);

        $targetChecklistItem = new ChecklistItem();
        $targetChecklistItem->text = 'item3';
        $targetChecklist->addChecklistItem($targetChecklistItem);

        // when
        $this->checklistNode->copyFromPrototype($this->checklistNodePrototype, new EntityMap($targetCamp));

        // then
        $this->assertCount(1, $this->checklistNode->getChecklistItems());
        $item1 = $this->checklistNode->getChecklistItems()[0];
        $this->assertEquals($this->itemPrototype3->text, $item1->text);
        $this->assertNotEquals($this->itemPrototype3->checklist->getCamp(), $item1->checklist->getCamp());
    }

    public function testCopyFromPrototypeAcrossCampsPrefersItemWithSameNameAndSameHierarchy() {
        // given
        $this->checklistNodePrototype->addChecklistItem($this->itemPrototype1);
        $this->checklistNodePrototype->addChecklistItem($this->itemPrototype3);
        $targetCamp = new Camp();

        $targetChecklist = new Checklist();
        $targetChecklist->name = 'target checklist';
        $targetCamp->addChecklist($targetChecklist);

        $targetChecklistItem1 = new ChecklistItem();
        $targetChecklistItem1->text = 'item3';
        $targetChecklist->addChecklistItem($targetChecklistItem1);
        $targetChecklistItem2 = new ChecklistItem();
        $targetChecklistItem2->text = 'item2';
        $targetChecklist->addChecklistItem($targetChecklistItem2);
        $targetChecklistItem3 = new ChecklistItem();
        $targetChecklistItem3->text = 'item3';
        $targetChecklistItem2->addChild($targetChecklistItem3);
        $targetChecklist->addChecklistItem($targetChecklistItem3);

        // when
        $this->checklistNode->copyFromPrototype($this->checklistNodePrototype, new EntityMap($targetCamp));

        // then
        $this->assertCount(1, $this->checklistNode->getChecklistItems());
        $resultItem = $this->checklistNode->getChecklistItems()[0];
        $this->assertEquals($targetChecklistItem3, $resultItem);
        $this->assertEquals($this->itemPrototype3->text, $resultItem->text);
        $this->assertNotEquals($this->itemPrototype3->checklist->getCamp(), $resultItem->checklist->getCamp());
        $this->assertEquals($targetCamp, $resultItem->checklist->getCamp());
    }

    public function testCopyFromPrototypeAcrossCampsPrefersItemWithSameChecklistPrototypeId() {
        // given
        $this->checklistNodePrototype->addChecklistItem($this->itemPrototype1);
        $this->checklistNodePrototype->addChecklistItem($this->itemPrototype3);
        $targetCamp = new Camp();

        $targetChecklist = new Checklist();
        $targetChecklist->name = 'checklist';
        $targetCamp->addChecklist($targetChecklist);

        $targetChecklist2 = new Checklist();
        $targetChecklist2->name = 'checklist with other name';
        $targetCamp->addChecklist($targetChecklist2);

        $targetChecklistItem1 = new ChecklistItem();
        $targetChecklistItem1->text = 'item2';
        $targetChecklist->addChecklistItem($targetChecklistItem1);
        $targetChecklistItem2 = new ChecklistItem();
        $targetChecklistItem2->text = 'item2';
        $targetChecklist->addChecklistItem($targetChecklistItem2);
        $targetChecklistItem3 = new ChecklistItem();
        $targetChecklistItem3->text = 'item3';
        $targetChecklistItem2->addChild($targetChecklistItem3);
        $targetChecklist2->addChecklistItem($targetChecklistItem3);
        $targetChecklistItem4 = new ChecklistItem();
        $targetChecklistItem4->text = 'item3';
        $targetChecklistItem1->addChild($targetChecklistItem4);
        $targetChecklist->addChecklistItem($targetChecklistItem4);

        $this->checklist->checklistPrototypeId = 'abc';
        $targetChecklist->checklistPrototypeId = 'abc';
        $targetChecklist2->checklistPrototypeId = 'def';

        // when
        $this->checklistNode->copyFromPrototype($this->checklistNodePrototype, new EntityMap($targetCamp));

        // then
        $this->assertCount(1, $this->checklistNode->getChecklistItems());
        $resultItem = $this->checklistNode->getChecklistItems()[0];
        $this->assertEquals($targetChecklistItem4, $resultItem);
        $this->assertEquals($this->itemPrototype3->text, $resultItem->text);
        $this->assertNotEquals($this->itemPrototype3->checklist->getCamp(), $resultItem->checklist->getCamp());
        $this->assertEquals($targetCamp, $resultItem->checklist->getCamp());
    }

    public function testCopyFromPrototypeAcrossCampsPrefersItemWithSameChecklistName() {
        // given
        $this->checklistNodePrototype->addChecklistItem($this->itemPrototype1);
        $this->checklistNodePrototype->addChecklistItem($this->itemPrototype3);
        $targetCamp = new Camp();

        $targetChecklist = new Checklist();
        $targetChecklist->name = 'checklist';
        $targetCamp->addChecklist($targetChecklist);

        $targetChecklist2 = new Checklist();
        $targetChecklist2->name = 'checklist with other name';
        $targetCamp->addChecklist($targetChecklist2);

        $targetChecklistItem1 = new ChecklistItem();
        $targetChecklistItem1->text = 'item2';
        $targetChecklist->addChecklistItem($targetChecklistItem1);
        $targetChecklistItem2 = new ChecklistItem();
        $targetChecklistItem2->text = 'item2';
        $targetChecklist->addChecklistItem($targetChecklistItem2);
        $targetChecklistItem3 = new ChecklistItem();
        $targetChecklistItem3->text = 'item3';
        $targetChecklistItem2->addChild($targetChecklistItem3);
        $targetChecklist2->addChecklistItem($targetChecklistItem3);
        $targetChecklistItem4 = new ChecklistItem();
        $targetChecklistItem4->text = 'item3';
        $targetChecklistItem1->addChild($targetChecklistItem4);
        $targetChecklist->addChecklistItem($targetChecklistItem4);

        // when
        $this->checklistNode->copyFromPrototype($this->checklistNodePrototype, new EntityMap($targetCamp));

        // then
        $this->assertCount(1, $this->checklistNode->getChecklistItems());
        $resultItem = $this->checklistNode->getChecklistItems()[0];
        $this->assertEquals($targetChecklistItem4, $resultItem);
        $this->assertEquals($this->itemPrototype3->text, $resultItem->text);
        $this->assertNotEquals($this->itemPrototype3->checklist->getCamp(), $resultItem->checklist->getCamp());
        $this->assertEquals($targetCamp, $resultItem->checklist->getCamp());
    }

    public function testCopyFromPrototypeAcrossCampsReusesExistingEntityMapping() {
        // given
        $this->checklistNodePrototype->addChecklistItem($this->itemPrototype1);
        $this->checklistNodePrototype->addChecklistItem($this->itemPrototype3);
        $targetCamp = new Camp();

        $targetChecklist = new Checklist();
        $targetChecklist->name = 'target checklist';
        $targetCamp->addChecklist($targetChecklist);

        $targetChecklistItem1 = new ChecklistItem();
        $targetChecklistItem1->text = 'item3-preferred';
        $targetChecklist->addChecklistItem($targetChecklistItem1);
        $targetChecklistItem2 = new ChecklistItem();
        $targetChecklistItem2->text = 'item2';
        $targetChecklist->addChecklistItem($targetChecklistItem2);
        $targetChecklistItem3 = new ChecklistItem();
        $targetChecklistItem3->text = 'item3';
        $targetChecklistItem2->addChild($targetChecklistItem3);
        $targetChecklist->addChecklistItem($targetChecklistItem3);

        $entityMap = new EntityMap($targetCamp);
        $entityMap->add($this->itemPrototype3, $targetChecklistItem1);

        // when
        $this->checklistNode->copyFromPrototype($this->checklistNodePrototype, $entityMap);

        // then
        $this->assertCount(1, $this->checklistNode->getChecklistItems());
        $item1 = $this->checklistNode->getChecklistItems()[0];
        $this->assertEquals($targetChecklistItem1->text, $item1->text);
        $this->assertNotEquals($this->itemPrototype3->checklist->getCamp(), $item1->checklist->getCamp());
    }
}
