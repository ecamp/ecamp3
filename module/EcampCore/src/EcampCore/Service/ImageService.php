<?php

namespace EcampCore\Service;

use EcampCore\Entity\Image;
use EcampCore\Entity\User;
use EcampCore\Repository\ImageRepository;

class ImageService extends Base\ServiceBase
{
    const DATA_URI_FORMAT = "/^data:([a-zA-Z0-9\/]+);base64,([a-zA-Z0-9\/+]*={0,2})$/";

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
    public function Get($image)
    {
        if ($image instanceof Image) {
            return $image;
        }

        return $this->imageRepository->find($image);
    }

    /**
     * @return Image
     */
    public function Create($owner = null)
    {
        $image = new Image();
        $this->persist($image);

        if ($owner instanceof User) {
            $owner->setImage($image);
        }

        return $image;
    }

    /**
     * @param $image
     * @param $data
     * @param null $mime
     */
    public function Save($image, $data, $mime = null)
    {
        if ($data) {
            $matches = array();
            if (preg_match(self::DATA_URI_FORMAT, $data, $matches)) {
                $mime = $matches[1];
                $data = base64_decode($matches[2]);
            }
        }

        $image = $this->Get($image);

        if ($mime != null) {
            $image->setMime($mime);
        }

        $image->setData($data);
    }

}
