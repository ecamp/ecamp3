<?php

namespace App\DataPersister\ContentNode;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\ContentNode\SingleText;

class SingleTextDataPersister extends ContentNodeBaseDataPersister implements ContextAwareDataPersisterInterface {
    public function supports($data, array $context = []): bool {
        return ($data instanceof SingleText) && $this->dataPersister->supports($data, $context);
    }

    /**
     * @param SingleText $data
     */
    public function onCreate($data) {
        if (isset($data->prototype)) {
            if (!($data->prototype instanceof SingleText)) {
                throw new \Exception('Prototype must be of type SingleText');
            }

            /** @var SingleText $prototype */
            $prototype = $data->prototype;

            if (!isset($data->text)) {
                $data->text = $prototype->text;
            }
        }

        parent::onCreate($data);
    }
}
