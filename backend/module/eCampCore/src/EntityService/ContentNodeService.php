<?php

namespace eCamp\Core\EntityService;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use eCamp\Core\ContentType\ContentTypeStrategyProvider;
use eCamp\Core\Entity\AbstractContentNodeOwner;
use eCamp\Core\Entity\ContentNode;
use eCamp\Core\Entity\ContentType;
use eCamp\Core\Hydrator\ContentNodeHydrator;
use eCamp\Core\Repository\ContentNodeRepository;
use eCamp\Lib\Acl\Acl;
use eCamp\Lib\Acl\NoAccessException;
use eCamp\Lib\Entity\BaseEntity;
use eCamp\Lib\Service\EntityValidationException;
use eCamp\Lib\Service\ServiceUtils;
use eCampApi\V1\Rest\ContentNode\ContentNodeCollection;
use Laminas\Authentication\AuthenticationService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ContentNodeService extends AbstractEntityService {
    private ContentTypeStrategyProvider $contentTypeStrategyProvider;
    private ?ContentNodeRepository $contentNodeRepository = null;

    public function __construct(
        ServiceUtils $serviceUtils,
        AuthenticationService $authenticationService,
        ContentTypeStrategyProvider $contentTypeStrategyProvider
    ) {
        parent::__construct(
            $serviceUtils,
            ContentNode::class,
            ContentNodeCollection::class,
            ContentNodeHydrator::class,
            $authenticationService
        );

        $this->contentTypeStrategyProvider = $contentTypeStrategyProvider;
    }

    public function createFromPrototype(AbstractContentNodeOwner $owner, ContentNode $prototype): ContentNode {
        /** @var ContentNode $contentNode */
        $contentNode = $this->create((object) [
            'ownerId' => $owner->getId(),
            'prototypeId' => $prototype->getId(),
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
        /** @var ContentNode $prototype */
        $prototype = null;

        if (isset($data->prototypeId)) {
            /** @var ContentNode $prototype */
            $prototype = $this->findRelatedEntity(ContentNode::class, $data, 'prototypeId');
            if (!isset($data->contentTypeId)) {
                $data->contentTypeId = $prototype->getContentType()->getId();
            }
            if (!isset($data->instanceName)) {
                $data->instanceName = $prototype->getInstanceName();
            }
            if (!isset($data->slot)) {
                $data->slot = $prototype->getSlot();
            }
            if (!isset($data->position)) {
                $data->position = $prototype->getPosition();
            }
            if (!isset($data->jsonConfig)) {
                $data->jsonConfig = $prototype->getConfig();
            }
        }

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

        $contentNode->setPosition(0);
        if (isset($data->position)) {
            $contentNode->setPosition($data->position);
        } elseif (null !== $contentNode->getParent()) {
            $position = $this->getContentNodeRepository()->getHighestChildPosition($contentNode->getParent(), $contentNode->getSlot());
            $contentNode->setPosition($position + 1);
        }

        if (isset($data->contentTypeName)) {
            /** @var EntityRepository $contentTypeRepository */
            $contentTypeRepository = $this->getServiceUtils()->emGetRepository(ContentType::class);
            /** @var ContentType $contentType */
            $contentType = $contentTypeRepository->findOneByName($data->contentTypeName);
        } else {
            $contentType = $this->findRelatedEntity(ContentType::class, $data, 'contentTypeId');
        }
        $contentNode->setContentType($contentType);

        $strategy = $this->contentTypeStrategyProvider->get($contentType);
        $strategy->contentNodeCreated($contentNode, $prototype);

        return $contentNode;
    }

    protected function fetchAllQueryBuilder($params = []): QueryBuilder {
        $q = parent::fetchAllQueryBuilder($params);

        if (isset($params['parentId'])) {
            $q->andWhere('row.parent = :parentId');
            $q->setParameter('parentId', $params['parentId']);
        }

        if (isset($params['ownerId'])) {
            $q->join('row.root', 'root');
            $q->join('root.owner', 'rootOwner');
            $q->andWhere('rootOwner.id = :ownerId');
            $q->setParameter('ownerId', $params['ownerId']);
        }

        return $q;
    }

    protected function patchEntity(BaseEntity $entity, $data): ContentNode {
        /** @var ContentNode $contentNode */
        $contentNode = parent::patchEntity($entity, $data);

        if (isset($data->parentId)) {
            /** @var ContentNode $parent */
            $parent = $this->findRelatedEntity(ContentNode::class, $data, 'parentId');
            $this->assertAllowed($parent, Acl::REST_PRIVILEGE_PATCH);

            // Disallow dragging across camps for now, because that has further implications
            if ($parent->getCamp()->getId() !== $contentNode->getCamp()->getId()) {
                throw (new EntityValidationException())->setMessages([
                    'parentId' => ['notSameCamp' => "Moving ContentNodes across camps is not implemented. Trying to move from camp {$contentNode->getCamp()->getId()} to camp {$parent->getCamp()->getId()}"],
                ]);
            }

            $contentNode->setParent($parent);
        }

        return $contentNode;
    }

    /**
     * @throws EntityValidationException
     */
    protected function validateEntity(BaseEntity $entity): void {
        /** @var ContentNode $contentNode */
        $contentNode = $entity;

        parent::validateEntity($contentNode);

        // Also allow the strategy to define custom validation
        $strategy = $this->contentTypeStrategyProvider->get($contentNode->getContentType());
        $strategy->validateContentNode($contentNode);
    }

    protected function getContentNodeRepository(): ContentNodeRepository {
        if (!$this->contentNodeRepository) {
            /** @var ContentNodeRepository $entityRepository */
            $entityRepository = $this->getServiceUtils()->emGetRepository(ContentNode::class);
            $this->contentNodeRepository = $entityRepository;
        }

        return $this->contentNodeRepository;
    }
}
