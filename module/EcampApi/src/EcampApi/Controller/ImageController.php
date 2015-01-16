<?php

namespace EcampApi\Controller;

use EcampLib\Controller\AbstractRestfulBaseController;
use Zend\Http\Response;

class ImageController extends AbstractRestfulBaseController
{

    /**
     * @return \EcampCore\Repository\ImageRepository
     */
    public function getImageRepository()
    {
        return $this->getServiceLocator()->get('EcampCore\Repository\Image');
    }

    /**
     * @return \EcampCore\Service\ImageService
     */
    public function getImageService()
    {
        return $this->getServiceLocator()->get('EcampCore\Service\Image');
    }

    public function showAction()
    {
        $imageId = $this->params('image');
        /** @var \EcampCore\Entity\Image $image */
        $image = $this->getImageRepository()->find($imageId);

        $response = $this->getResponse();

        $response->setContent($image->getData());
        $response->getHeaders()
            ->addHeaderLine('Content-Transfer-Encoding', 'binary')
            ->addHeaderLine('Content-Type', $image->getMime())
            ->addHeaderLine('Content-Length', $image->getSize());

        return $response;
    }

    public function saveAction()
    {
        $imageId = $this->params('image');
        $mime = null;
        $content = $this->getRequest()->getContent();

        if($this->getRequest()->isPost()){
            $dataUriFormat = "/^data:([a-zA-Z0-9\/]+);base64,([a-zA-Z0-9\/+]*={0,2})$/";

            $matches = array();
            if(preg_match($dataUriFormat, $content, $matches)){
                $mime = $matches[1];
                $content = base64_decode($matches[2]);
            }

            $this->getImageService()->Save($imageId, $content, $mime);

            /** @var \Zend\Http\Response $response */
            $response = $this->getResponse();
            $response->setStatusCode(Response::STATUS_CODE_204);

            return $response;

        } else {
            throw new \Exception("Image upload is only with Method POST possible");
        }
    }

}