<?php

namespace App\Post;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class PostImageManager implements PostImageManagerInterface
{
    public function __construct(
        private readonly string $postImageFolder,
        private readonly Filesystem $filesystem,
        private readonly SluggerInterface $slugger
    )
    {

    }

    public function getImageFolder(string $filename): string
    {
        $firstChar = substr($filename, 0, 1);

        return "{$this->postImageFolder}/{$firstChar}";
    }

    public function moveImage(UploadedFile $image): string
    {
        $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();

        $path = $this->getImageFolder($newFilename);

        if (false === $this->filesystem->exists($path)) {
            $this->filesystem->mkdir($path);
        }

        $image->move($path, $newFilename);
        return $newFilename;
    }

    public function getHash(\SplFileInfo $file): string
    {
        return hash_file('sha256', $file->getRealPath());
    }
}
