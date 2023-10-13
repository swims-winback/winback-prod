<?php
namespace App\Services;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImageUploader {

    private $targetDirectory;
    private $slugger;

    public function __construct($targetDirectory, SluggerInterface $slugger)
    {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }

    public function upload(UploadedFile $file, $targetFolder)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $filename = $originalFilename.'.'."bin";

        try {
            //print_r($this->getTargetDirectory() . $targetFolder . $filename);
            if (file_exists($this->getTargetDirectory() . $targetFolder . $filename)) {
                unlink($this->getTargetDirectory() . $targetFolder . $filename);
            }
            $file->move($this->getTargetDirectory().$targetFolder, $filename);
        } catch (FileException $e) {
            echo "file error";
            throw $e;
        }

        return $filename;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}