<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class UploaderService
{
    private $param;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->param = $parameterBag;
    }


    public function uploadImage($file): string
    {
        try {
			
            $fileName = uniqid('image-') . '.' . $file->guessExtension();
            $file->move($this->param->get('uploads_images_directory'), $fileName);

            return $fileName;
        } catch (\Exception $e) {
            throw new \Exception('An error occured while uploading the image: ' . $e->getMessage());
        }
    }
	public function deleteImage (string $fileName) : void
	{
	}

}