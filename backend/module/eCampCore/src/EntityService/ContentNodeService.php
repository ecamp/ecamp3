<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\ORMException;
use eCamp\Core\ContentType\ContentTypeStrategyProvider;
use eCamp\Core\Entity\AbstractContentNodeOwner;
use eCamp\Core\Entity\ContentNode;
use eCamp\Core\Entity\ContentType;
use eCamp\Core\Hydrator\ContentNodeHydrator;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Entity\BaseEntity;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ContentNodeService extends AbstractEntityService {
    private ContentTypeStrategyProvider $contentTypeStrategyProvider;

    public function __construct(
        ServiceUtils $serviceUtils,
        AuthenticationService $authenticationService,
        ContentTypeStrategyProvider $contentTypeStrategyProvider
    ) {
        parent::__construct(
            $serviceUtils,
            ContentNode::class,
            ContentNodeHydrator::class,
            $authenticationService
        );

        $this->contentTypeStrategyProvider = $contentTypeStrategyProvider;
    }

    public function createFromPrototype(AbstractContentNodeOwner $owner, ContentNode $prototype): ContentNode {
        /** @var ContentNode $contentNode */
        $contentNode = $this->create((object) [
            'ownerId' => $owner->getId(),
            'contentTypeId' => $prototype->getContentType()->getId(),
            'instanceName' => $prototype->getInstanceName(),
            'slot' => $prototype->getSlot(),
            'position' => $prototype->getPosition(),
            'config' => $prototype->getConfig(),
        ]);

        foreach ($prototype->getChildren() as $childPrototype) {
            $childContentNode = $this->createFromPrototype($owner, $childPrototype);
            $childContentNode->setParent($contentNode);
        }

        return $contentNode;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ORMException
     * @throws NoAccessException
     */
    protected function createEntity($data): ContentNode {
        /** @var ContentNode $contentNode */
        $contentNode = parent::createEntity($data);

        if (isset($data->ownerId)) {
            /** @var AbstractContentNodeOwner $owner */
            $owner = $this->findRelatedEntity(AbstractContentNodeOwner::class, $data, 'ownerId');
            $this->assertAllowed($owner, Acl::REST_PRIVILEGE_PATCH);
            $owner->setRootContentNode($contentNode);
        }
        if (isset($data->parentId)) {
            /** @var ContentNode $parent */
            $parent = $this->findRelatedEntity(ContentNode::class, $data, 'parentId');
            $this->assertAllowed($parent, Acl::REST_PRIVILEGE_PATCH);
            $contentNode->setParent($parent);
        }

        /** @var ContentType $contentType */
        $contentType = $this->findRelatedEntity(ContentType::class, $data, 'contentTypeId');
        $contentNode->setContentType($contentType);

        $strategy = $this->contentTypeStrategyProvider->get($contentType);
        $strategy->contentNodeCreated($contentNode);

        return $contentNode;
    }

    protected function patchEntity(BaseEntity $entity, $data): ContentNode {
        /** @var ContentNode $contentNode */
        $contentNode = parent::patchEntity($entity, $data);

        if (isset($data->parentId)) {
            /** @var ContentNode $parent */
            $parent = $this->findRelatedEntity(ContentNode::class, $data, 'parentId');
            $this->assertAllowed($parent, Acl::REST_PRIVILEGE_PATCH);
            $contentNode->setParent($parent);
        }

        return $entity;
    }
}
