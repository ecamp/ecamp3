<?php

namespace App\DataPersister\ContentNode;

use App\DataPersister\Util\DataPersisterObservable;
use App\Entity\ContentNode\Storyboard;
use App\Entity\ContentNode\StoryboardSection;

class StoryboardDataPersister extends ContentNodeAbstractDataPersister {
    /**
     * @throws \ReflectionException
     */
    public function __construct(
        DataPersisterObservable $dataPersisterObservable
    ) {
        parent::__construct(
            Storyboard::class,
            $dataPersisterObservable
        );
    }

    public function beforeCreate($data): Storyboard {
        /** @var Storyboard $storyboard */
        $storyboard = $data;

        if (isset($storyboard->prototype)) {
            if (!($storyboard->prototype instanceof Storyboard)) {
                throw new \Exception('Prototype must be of type Storyboard');
            }

            // copy all storyboard sections
            foreach ($storyboard->prototype->sections as $prototypeSection) {
                $section = new StoryboardSection();

                $section->column1 = $prototypeSection->column1;
                $section->column2 = $prototypeSection->column2;
                $section->column3 = $prototypeSection->column3;
                $section->setPos($prototypeSection->getPos());

                $storyboard->addSection($section);
            }
        }

        return parent::beforeCreateContentNode($storyboard);
    }
}
