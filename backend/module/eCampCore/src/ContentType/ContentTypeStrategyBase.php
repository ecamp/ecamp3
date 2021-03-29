<?php

namespace eCamp\Core\ContentType;

use eCamp\Core\Entity\ContentNode;
use eCamp\Lib\Service\EntityValidationException;
use eCamp\Lib\Service\ServiceUtils;
use Swaggest\JsonSchema\Exception;
use Swaggest\JsonSchema\InvalidValue;
use Swaggest\JsonSchema\Schema;
use Swaggest\JsonSchema\SchemaContract;

abstract class ContentTypeStrategyBase implements ContentTypeStrategyInterface {
    private ServiceUtils $serviceUtils;
    protected ?SchemaContract $jsonConfigSchema = null;

    public function __construct(ServiceUtils $serviceUtils) {
        $this->serviceUtils = $serviceUtils;
    }

    abstract public function contentNodeExtract(ContentNode $contentNode): array;

    abstract public function contentNodeCreated(ContentNode $contentNode): void;

    /**
     * @param ContentNode $contentNode
     * @throws EntityValidationException
     */
    public function validateContentNode(ContentNode $contentNode): void {
        $this->validateJsonConfig($contentNode);
    }

    protected function getJsonConfigSchema(): array {
        // The default is to have a `null` jsonConfig.
        // If a contentType needs to use jsonConfig, the strategy needs to explicitly override this.
        return ['type' => 'null'];
    }

    /**
     * @param ContentNode $contentNode
     * @throws EntityValidationException
     */
    protected function validateJsonConfig(ContentNode $contentNode): void {
        try {
            if (!$this->jsonConfigSchema) {
                // Re-encode and decode the schema value, because the schema checker needs
                // objects to be represented as stdObjects
                $this->jsonConfigSchema = Schema::import(json_decode(json_encode($this->getJsonConfigSchema())));
            }

            // Re-encode and decode the input value, because the schema checker needs
            // objects to be represented as stdObjects
            $this->jsonConfigSchema->in(json_decode(json_encode($contentNode->getJsonConfig())));
        } catch (InvalidValue|Exception $exception) {
            throw (new EntityValidationException())->setMessages([
                'jsonConfig' => ['invalid' => $exception->getMessage()]
            ]);
        }
    }

    protected function getServiceUtils(): ServiceUtils {
        return $this->serviceUtils;
    }
}
