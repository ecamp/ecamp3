<?php

namespace EcampApi\Controller;

use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use EcampCore\Entity\Image;

class AssetsController extends AbstractActionController
{
	/** @var EntityManager */
	private $entityManager;
	
	public function __construct($entityManager){
		$this->entityManager = $entityManager;
	}
	
	
	public function apiV1ImageAction(){
		$id = $this->params()->fromRoute('image_id');
		
		/** @var Image $image */
		$image = $this->entityManager->find(Image::class, $id);
		
		$response = $this->getResponse();
		
		if ($image != null) {
			$mime = $image->getMime();
			$content = $image->getData();
			
			$response->setContent($content);
			$response
				->getHeaders()
				->addHeaderLine('Content-Type', $mime)
				->addHeaderLine('Content-Length', mb_strlen($content))
				->addHeaderLine('Content-Transfer-Encoding', 'binary');
			
		} else {
			$response->setStatusCode(404);
		}
		
		return $response;
	}
}
