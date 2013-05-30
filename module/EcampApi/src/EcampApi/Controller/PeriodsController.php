<?php

namespace EcampApi\Controller;

use EcampApi\Serializer\PeriodSerializer;
use EcampLib\Controller\AbstractRestfulBaseController;
use EcampCore\Repository\Provider\PeriodRepositoryProvider;

use Zend\View\Model\JsonModel;

class PeriodsController extends AbstractRestfulBaseController
	implements PeriodRepositoryProvider
{
	
	public function getList(){
		$periods = $this->ecampCore_PeriodRepo()->findAll();
		
		$periodSerializer = new PeriodSerializer(
			$this->params('format'), $this->getEvent()->getRouter());
		
		return new JsonModel($periodSerializer($periods));
	}
	
	public function get($id){
		$period = $this->ecampCore_PeriodRepo()->find($id);
		
		$periodSerializer = new PeriodSerializer(
			$this->params('format'), $this->getEvent()->getRouter());
		
		return new JsonModel($periodSerializer($period));
	}
	
	public function head($id = null){
        $format = $this->params('format');
		die("head." . $format);
	}
	
	public function create($data){
        $format = $this->params('format');
		die("create." . $format);
	}
	
	public function update($id, $data){
        $format = $this->params('format');
		die("update." . $format);
	}
	
	public function delete($id){
        $format = $this->params('format');
		die("delete." . $format);
	}
}
