<?php

namespace App\Post;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface PostImageManagerInterface
{
    /**
     * Move the image in the storing path and returns the image name
     * @param UploadedFile $image
     * @return string
     */
    public function moveImage(UploadedFile $image): string;

    /**
     * Returns the hash of the file
     * @param \SplFileInfo $file
     * @return string
     */
    public function getHash(\SplFileInfo $file): string;

    public function getImageFolder(string $filename): string;
}
