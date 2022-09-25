<?php

namespace App\DataPersister\ContentNode;

use App\DataPersister\Util\DataPersisterObservable;
use App\Entity\ContentNode\Storyboard;
use App\InputFilter\CleanHTMLFilter;
use Ramsey\Uuid\Uuid;

class StoryboardDataPersister extends ContentNodeAbstractDataPersister {
    /**
     * @throws \ReflectionException
     */
    public function __construct(
        DataPersisterObservable $dataPersisterObservable,
        private CleanHTMLFilter $cleanHTMLFilter
    ) {
        parent::__construct(
            Storyboard::class,
            $dataPersisterObservable
        );
    }

    /**
     * @param Storyboard $data
     */
    public function beforeCreate($data): Storyboard {
        $data = parent::beforeCreate($data);

        if (empty($data->data['sections'])) {
            $data->data['sections'][Uuid::uuid4()] = [
                'column1' => '',
                'column2' => '',
                'column3' => '',
                'position' => 0,
            ];
        }

        return $this->sanitizeData($data);
    }

    /**
     * @param Storyboard $data
     */
    public function beforeUpdate($data): Storyboard {
        $data = parent::beforeUpdate($data);

        return $this->sanitizeData($data);
    }

    private function sanitizeData($data) {
        foreach ($data->data['sections'] as &$section) {
            $section = $this->cleanHTMLFilter->applyTo($section, 'column1');
            $section = $this->cleanHTMLFilter->applyTo($section, 'column2');
            $section = $this->cleanHTMLFilter->applyTo($section, 'column3');
        }

        return $data;
    }
}
