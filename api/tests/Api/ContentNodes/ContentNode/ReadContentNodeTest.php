<?php

namespace App\Tests\Api\ContentNodes\ContentNode;

use App\Entity\ContentNode;
use App\Tests\Api\ECampApiTestCase;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @internal
 */
class ReadContentNodeTest extends ECampApiTestCase {
    // TODO security tests when not logged in or not collaborator

    public function testGetSingleContentNodeIsAllowedForCollaborator() {
        /** @var ContentNode $contentNode */
        $contentNode = static::$fixtures['columnLayoutChild1'];
        $children = new ArrayCollection($contentNode->getChildren());
        $childrenIris = $children->map(fn (ContentNode $contentNode) => $this->getIriFor($contentNode))->toArray();

        static::createClientWithCredentials()->request('GET', '/content_nodes/'.$contentNode->getId());
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $contentNode->getId(),
            'instanceName' => $contentNode->instanceName,
            'slot' => $contentNode->slot,
            'position' => $contentNode->position,
            'contentTypeName' => $contentNode->getContentTypeName(),
            'data' => $contentNode->data,
            '_links' => [
                'parent' => ['href' => $this->getIriFor($contentNode->parent)],
                // For performance reasons, children must be an array of hrefs, not a single href to a collection!
                // If this test breaks here, that means you probably added a "parent" filter on ContentNode.
                // If you really need that filter, see https://github.com/ecamp/ecamp3/pull/2571#discussion_r844089434
                // for more info on why it was previously removed. You will then probably have to adapt
                // RelatedCollectionLinkNormalizer and add a way to explicitly disable related collection links on a
                // specific relation, so that the "parent" filter can co-exist with children being an array.
                'children' => $childrenIris,
                'self' => ['href' => $this->getIriFor('columnLayoutChild1')],
            ],
        ]);
    }

    public function testGetSingleContentNodeIsAllowedInCampPrototype() {
        // given
        /** @var ContentNode $contentNode */
        $contentNode = static::$fixtures['columnLayout1campPrototype'];

        // when (requesting with anonymous user)
        static::createBasicClient()->request('GET', '/content_nodes/'.$contentNode->getId());

        // then
        $this->assertResponseStatusCodeSame(200);
    }

    public function testGetSingleContentNodeIncludesProperRelationLinks() {
        /** @var Storyboard $contentNode */
        $contentNode = static::$fixtures['materialNode1'];

        // when content node is loaded via generic /content_nodes endpoint
        static::createClientWithCredentials()->request('GET', '/content_nodes/'.$contentNode->getId());

        // then the response still includes content-node (here:MaterialNode) specific relation links (injected from RelatedCollectionLinkNormalizer)
        $this->assertJsonContains([
            '_links' => [
                'materialItems' => ['href' => '/material_items?materialNode='.urlencode($this->getIriFor($contentNode))],
            ],
        ]);
        $this->assertResponseStatusCodeSame(200);
    }
}
