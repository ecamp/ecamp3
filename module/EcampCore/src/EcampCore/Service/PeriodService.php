<?php

namespace EcampCore\Service;

use EcampCore\Entity\Day;
use EcampCore\Entity\Period;
use EcampLib\Service\AbstractService;
use ZF\Apigility\Doctrine\Server\Resource\DoctrineResource;

class PeriodService extends AbstractService
{

    private $dayService;

    public function __construct(DoctrineResource $doctrineResource, DayService $dayService)
    {
        parent::__construct($doctrineResource);
        $this->dayService = $dayService;
    }

    public function create($data){
        /** @var Period $period */
        $period = parent::create($data);
        $this->updateDays($period, $data);

        return $period;
    }

    public function update($id, $data){
        /** @var Period $period */
        $period = parent::update($id, $data);
        $this->updateDays($period, $data);

        return $period;
    }

    public function patch($id, $data){
        /** @var Period $period */
        $period = parent::patch($id, $data);
        $this->updateDays($period, $data);

        return $period;
    }

    private function updateDays(Period $period, $data)
    {

        $start = $period->getStart();
        $end   = new \DateTime($data->end);

        if(isset($end)){
            $numOfDays = ($end->getTimestamp() - $start->getTimestamp())/(24 * 60 * 60) + 1;
            $oldNumOfDays = $period->getDays()->count();


            if ($oldNumOfDays < $numOfDays) {
                for ($offset = $oldNumOfDays; $offset < $numOfDays; $offset++) {
                    $this->dayService->create(['period' => $period->getId(), 'offset' => $offset]);
                }
            }

            if ($oldNumOfDays > $numOfDays) {
                for ($offset = $numOfDays; $offset < $oldNumOfDays; $offset++) {
                    $day = $period->getDays()->last();
                    if($day){ $this->dayService->delete($day->getId()); }
                }
            }

        }

        $this->getObjectManager()->flush();

    }

}