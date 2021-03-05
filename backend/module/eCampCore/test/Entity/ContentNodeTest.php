<?php

namespace eCamp\CoreTest\Entity;

use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\ContentNode;
use eCamp\Core\Entity\ContentType;
use eCamp\LibTest\PHPUnit\AbstractTestCase;

/**
 * @internal
 */
class ContentNodeTest extends AbstractTestCase {
    public function testContentNode(): void {
        $camp = new Camp();

        $contentType = new ContentType();

        $activity = new Activity();
        $activity->setCamp($camp);
        $activity->setTitle('ActivityTitle');

        $contentNode = new ContentNode();
        $contentNode->setOwner($activity);
        $contentNode->setContentType($contentType);
        $contentNode->setInstanceName('ContentNodeName');
        $contentNode->setSlot('slot');
        $contentNode->setPosition(1);

        $this->assertEquals($activity, $contentNode->getOwner());
        $this->assertEquals($contentType, $contentNode->getContentType());
        $this->assertEquals('ContentNodeName', $contentNode->getInstanceName());
        $this->assertEquals('slot', $contentNode->getSlot());
        $this->assertEquals(1, $contentNode->getPosition());
        $this->assertEquals($camp, $contentNode->getCamp());
    }

    public function testSingleContentNode(): void {
        $contentNode = new ContentNode();

        $this->assertTrue($contentNode->isRoot());
        $this->assertNull($contentNode->getParent());
        $this->assertEquals($contentNode, $contentNode->getRoot());
        $this->assertCount(0, $contentNode->getMyChildren());
        $this->assertCount(0, $contentNode->getAllChildren());
    }

    public function testContentNodeHierarchy(): void {
        $root = new ContentNode();
        $node = new ContentNode();
        $child = new ContentNode();

        $node->setParent($root);
        $child->setParent($node);

        $this->assertTrue($root->isRoot());

        $this->assertNull($root->getParent());
        $this->assertCount(1, $root->getMyChildren());
        $this->assertContains($node, $root->getMyChildren());

        $this->assertCount(2, $root->getAllChildren());
        $this->assertContains($node, $root->getAllChildren());
        $this->assertContains($child, $root->getAllChildren());
        $this->assertEquals($root, $node->getRoot());
        $this->assertEquals($root, $child->getRoot());
    }

    public function testChangeParent(): void {
        $root = new ContentNode();
        $node1 = new ContentNode();
        $node2 = new ContentNode();
        $child = new ContentNode();

        $node1->setParent($root);
        $node2->setParent($root);
        $child->setParent($node1);

        $this->assertEquals($root, $child->getRoot());
        $this->assertEquals($node1, $child->getParent());
        $this->assertContains($child, $node1->getMyChildren());
        $this->assertNotContains($child, $node2->getMyChildren());

        $child->setParent($node2);

        $this->assertEquals($root, $child->getRoot());
        $this->assertEquals($node2, $child->getParent());
        $this->assertNotContains($child, $node1->getMyChildren());
        $this->assertContains($child, $node2->getMyChildren());
    }

    public function testChangeRoot(): void {
        $root1 = new ContentNode();
        $root2 = new ContentNode();
        $node = new ContentNode();
        $child = new ContentNode();

        $node->setParent($root1);
        $child->setParent($node);

        $this->assertEquals($root1, $node->getRoot());
        $this->assertEquals($root1, $child->getRoot());
        $this->assertContains($child, $root1->getallChildren());
        $this->assertNotContains($child, $root2->getAllChildren());

        $node->setParent($root2);

        $this->assertEquals($root2, $node->getRoot());
        $this->assertEquals($root2, $child->getRoot());
        $this->assertNotContains($child, $root1->getallChildren());
        $this->assertContains($child, $root2->getAllChildren());
    }

    public function testDetachNode(): void {
        $root = new ContentNode();
        $node = new ContentNode();
        $child = new ContentNode();

        $node->setParent($root);
        $child->setParent($node);
        $this->assertEquals($root, $child->getRoot());

        $node->setParent(null);
        $this->assertEquals(null, $node->getParent());
        $this->assertEquals($node, $node->getRoot());
        $this->assertEquals($node, $child->getRoot());

        $child->setParent(null);
        $this->assertEquals($child, $child->getRoot());
        $this->assertEquals(null, $child->getParent());
    }
}
