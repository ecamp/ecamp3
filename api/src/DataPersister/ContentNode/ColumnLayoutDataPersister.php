<?php

namespace App\DataPersister\ContentNode;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\ContentNode\ColumnLayout;

class ColumnLayoutDataPersister extends ContentNodeBaseDataPersister implements ContextAwareDataPersisterInterface {
    public function supports($data, array $context = []): bool {
        return ($data instanceof ColumnLayout) && $this->dataPersister->supports($data, $context);
    }

    /**
     * @param ColumnLayout $data
     */
    public function onCreate($data) {
        if (isset($data->prototype)) {
            if (!($data->prototype instanceof ColumnLayout)) {
                throw new \Exception('Prototype must be of type ColumnLayout');
            }
        }

        parent::onCreate($data);
    }
}
