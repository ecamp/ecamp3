<?php

namespace EcampCore\Service;

use EcampCore\Entity\Image;
use EcampCore\Repository\ImageRepository;
use EcampLib\Service\ServiceBase;

class ImageService extends ServiceBase
{
    /**
     * @var \EcampCore\Repository\ImageRepository
     */
    private $imageRepository;


    public function __construct(ImageRepository $imageRepository)
    {
        $this->imageRepository = $imageRepository;
    }

    /**
     * @return \EcampCore\Entity\Image
     */
    public function Get($image){
        if($image instanceof Image){
            return $image;
        }

        return $this->imageRepository->find($image);
    }

    public function Save($image, $data, $mime = null)
    {
        $image = $this->Get($image);

        if($mime != null) {
            $image->setMime($mime);
        }
        $image->setData($data);
    }

}