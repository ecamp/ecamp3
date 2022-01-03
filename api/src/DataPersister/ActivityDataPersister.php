<?php

namespace App\DataPersister;

use App\DataPersister\Util\AbstractDataPersister;
use App\DataPersister\Util\DataPersisterObservable;
use App\Entity\Activity;
use App\Entity\ActivityResponsible;
use App\Entity\CampCollaboration;
use App\Entity\ContentNode\ColumnLayout;

class ActivityDataPersister extends AbstractDataPersister {
    public function __construct(
        DataPersisterObservable $dataPersisterObservable
    ) {
        parent::__construct(
            Activity::class,
            $dataPersisterObservable,
        );
    }

    /**
     * @param Activity $data
     */
    public function beforeCreate($data): Activity {
        $data->camp = $data->category?->camp;

        if (!isset($data->category?->rootContentNode)) {
            throw new \UnexpectedValueException('Property rootContentNode of provided category is null. Object of type '.ColumnLayout::class.' expected.');
        }

        if (!is_a($data->category->rootContentNode, ColumnLayout::class)) {
            throw new \UnexpectedValueException('Property rootContentNode of provided category is of wrong type. Object of type '.ColumnLayout::class.' expected.');
        }

        $rootContentNode = new ColumnLayout();
        $data->setRootContentNode($rootContentNode);

        // deep copy from category root node
        $rootContentNode->copyFromPrototype($data->category->rootContentNode);

        $this->updateCampCollaborations($data);

        return $data;
    }

    /**
     * @param Activity $data
     */
    public function beforeUpdate($data): Activity {
        $this->updateCampCollaborations($data);

        return $data;
    }

    /**
     * add and removes acitivityResponsibles based on $data->campCollaborations
     * assumes $data->campCollaborations to be an array of valid CampCollaboration IRIs
     * API platform automatically replaces the IRIs with entities before calling the data persister.
     */
    private function updateCampCollaborations(Activity $activity) {
        // add new camp collaborations
        foreach ($activity->campCollaborations as $campCollaboration) {
            $activity->addCampCollaboration($campCollaboration);
        }

        // remove old/existing camp collaborations
        $listOfNewCampCollaborationIds = array_map(function (CampCollaboration $campCollaboration) {
            return $campCollaboration->getId();
        }, $activity->campCollaborations);

        $activityResponsiblesToRemove = $activity->activityResponsibles->filter(function (ActivityResponsible $activityResponsible) use ($listOfNewCampCollaborationIds) {
            return !in_array($activityResponsible->campCollaboration?->getId(), $listOfNewCampCollaborationIds);
        });

        foreach ($activityResponsiblesToRemove as $activityResponsible) {
            $activity->removeActivityResponsible($activityResponsible);
        }
    }
}
