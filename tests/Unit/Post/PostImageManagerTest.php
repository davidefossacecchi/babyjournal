<?php

namespace App\Tests\Unit\Post;

use App\Post\PostImageManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class PostImageManagerTest extends KernelTestCase
{
    public function testPathIsCorrect(): void
    {
        $filesystem = $this->createMock(Filesystem::class);
        $slugger = $this->createMock(SluggerInterface::class);

        $postImageManager = new PostImageManager('/tmp', $filesystem, $slugger);

        $fileNames = [
            'Image 1.jpg' => '/tmp/i',
            'image nuova.png' => '/tmp/i',
            '_Z_image.gif' => '/tmp/z',
            ' Cane.jpg' => '/tmp/c',
            '1_imagine.jpg' => '/tmp/1',
        ];

        foreach ($fileNames as $fileName => $expectedPath) {
            $this->assertEquals($expectedPath, $postImageManager->getImageFolder($fileName));
        }

        // filename without alphanumeric characters should throw an exception
        $this->expectException(\InvalidArgumentException::class);
        $postImageManager->getImageFolder('!#+ò#.jpg');

        // empty filename should throw an exception
        $this->expectException(\InvalidArgumentException::class);
        $postImageManager->getImageFolder('.jpg');
    }

    public function testFileMove(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        $slugger = $container->get(SluggerInterface::class);

        $path = '/tmp';
        $uploadedFile = $this->createMock(UploadedFile::class);

        $uploadedFile->method('getClientOriginalName')->willReturn('Image 1.jpg');
        $uploadedFile->method('guessExtension')->willReturn('jpg');
        $uploadedFile->expects($this->once())
            ->method('move')
            ->with(
                $path.'/i',
            $this->callback(function($arg) {
                return (bool) preg_match('/^Image-1-[0-9a-f]+\.jpg$/', $arg);
            }));

        $filesystem = $this->createMock(Filesystem::class);

        // test folder creation during the process
        $filesystem->method('exists')->willReturn(false);
        $filesystem->expects($this->once())
            ->method('mkdir')
            ->with('/tmp/i');

        $postImageManager = new PostImageManager($path, $filesystem, $slugger);
        $postImageManager->moveImage($uploadedFile);
    }
}
