<?php

namespace App\DataPersister\ContentNode;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\ContentNode\Storyboard;
use App\Entity\ContentNode\StoryboardSection;

class StoryboardDataPersister extends ContentNodeBaseDataPersister implements ContextAwareDataPersisterInterface {
    public function supports($storyboard, array $context = []): bool {
        return ($storyboard instanceof Storyboard) && $this->dataPersister->supports($storyboard, $context);
    }

    /**
     * @param Storyboard $storyboard
     */
    public function onCreate($storyboard) {
        if (isset($storyboard->prototype)) {
            if (!($storyboard->prototype instanceof Storyboard)) {
                throw new \Exception('Prototype must be of type Storyboard');
            }

            /** @var Storyboard $prototype */
            $prototype = $storyboard->prototype;

            // copy all storyboard sections
            foreach ($prototype->sections as $prototypeSection) {
                $section = new StoryboardSection();

                $section->column1 = $prototypeSection->column1;
                $section->column2 = $prototypeSection->column2;
                $section->column3 = $prototypeSection->column3;

                $storyboard->addSection($section);
            }
        }

        parent::onCreate($storyboard);
    }
}
