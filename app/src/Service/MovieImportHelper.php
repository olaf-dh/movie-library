<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Movie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MovieImportHelper
{
    const MEDIA_FILES_PATH = '../mediaFiles/';

    private EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $file
     * @return mixed
     */
    private function fileToArrayConverter($file): mixed
    {
        $contents = file_get_contents($file);

        return json_decode($contents);
    }

    /**
     * @param UploadedFile $file
     */
    public function importMovies(UploadedFile $file): void
    {
        $movieArray = $this->fileToArrayConverter(self::MEDIA_FILES_PATH . $file->getClientOriginalName());

        foreach ($movieArray as $item) {
            $directory = $item->folder;
            $imageFile = $item->picture;
            $subtitleFile = $item->subTitles;
            $fileName = substr($item->title, 0, -4);
            $year = (int)substr($item->folder, -5, 4);
            $fileType = substr($item->title, -3);
            $fileSize = (int)round($item->fileSize / 1024 / 1024);
            $title = substr($item->folder, 0, -6);

            $newMovie = new Movie();
            $newMovie
                ->setTitle($title)
                ->setYear($year)
                ->setDirectory($directory)
                ->setFileType($fileType)
                ->setImageFile($imageFile)
                ->setSubtitleFile($subtitleFile)
                ->setFileName($fileName)
                ->setFileSize($fileSize)
            ;
            $this->entityManager->persist($newMovie);
        }
        $this->entityManager->flush();
    }
}
