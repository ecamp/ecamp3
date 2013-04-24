<?php

namespace EcampStoryboard\Controller;


use EcampCore\Controller\AbstractRestfulBaseController;
use EcampStoryboard\Repository\Provider\SectionRepositoryProvider;
use EcampStoryboard\Serializer\SectionSerializer;

use Zend\View\Model\JsonModel;

class SectionController extends AbstractRestfulBaseController
	implements SectionRepositoryProvider
{
	
	public function getList(){
		$sections = $this->ecampStoryboard_SectionRepo()->findAll();
		
		$sectionSerializer = new SectionSerializer(
				$this->params('format'), $this->getEvent()->getRouter());
		
		return new JsonModel($sectionSerializer($sections));
	}
	
	public function get($id){
		$section = $this->ecampStoryboard_SectionRepo()->find($id);
		
		$sectionSerializer = new SectionSerializer(
				$this->params('format'), $this->getEvent()->getRouter());
		
		return new JsonModel($sectionSerializer($section));
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
