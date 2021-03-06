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
        $jsonConfig = ['a' => 2];

        $activity = new Activity();
        $activity->setCamp($camp);
        $activity->setTitle('ActivityTitle');

        $contentNode = new ContentNode();
        $contentNode->setOwner($activity);
        $contentNode->setContentType($contentType);
        $contentNode->setInstanceName('ContentNodeName');
        $contentNode->setSlot('slot');
        $contentNode->setPosition(1);
        $contentNode->setJsonConfig($jsonConfig);

        $this->assertEquals($activity, $contentNode->getOwner());
        $this->assertEquals($contentType, $contentNode->getContentType());
        $this->assertEquals('ContentNodeName', $contentNode->getInstanceName());
        $this->assertEquals('slot', $contentNode->getSlot());
        $this->assertEquals(1, $contentNode->getPosition());
        $this->assertEquals($jsonConfig, $contentNode->getJsonConfig());
        $this->assertEquals(2, $contentNode->getConfig('a'));
        $this->assertEquals($camp, $contentNode->getCamp());
    }

    public function testSingleContentNode(): void {
        $contentNode = new ContentNode();

        $this->assertTrue($contentNode->isRoot());
        $this->assertNull($contentNode->getParent());
        $this->assertEquals($contentNode, $contentNode->getRoot());
        $this->assertCount(0, $contentNode->getChildren());
        $this->assertCount(1, $contentNode->getRootDescendants());
    }

    public function testContentNodeHierarchy(): void {
        $root = new ContentNode();
        $node = new ContentNode();
        $child = new ContentNode();

        $node->setParent($root);
        $child->setParent($node);

        $this->assertTrue($root->isRoot());

        $this->assertNull($root->getParent());
        $this->assertCount(1, $root->getChildren());
        $this->assertContains($node, $root->getChildren());

        $this->assertCount(3, $root->getRootDescendants());
        $this->assertContains($node, $root->getRootDescendants());
        $this->assertContains($child, $root->getRootDescendants());
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
        $this->assertContains($child, $node1->getChildren());
        $this->assertNotContains($child, $node2->getChildren());

        $child->setParent($node2);

        $this->assertEquals($root, $child->getRoot());
        $this->assertEquals($node2, $child->getParent());
        $this->assertNotContains($child, $node1->getChildren());
        $this->assertContains($child, $node2->getChildren());
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
        $this->assertContains($child, $root1->getRootDescendants());
        $this->assertNotContains($child, $root2->getRootDescendants());

        $node->setParent($root2);

        $this->assertEquals($root2, $node->getRoot());
        $this->assertEquals($root2, $child->getRoot());
        $this->assertNotContains($child, $root1->getRootDescendants());
        $this->assertContains($child, $root2->getRootDescendants());
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
