<?php

namespace App\Twig;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use App\Entity\Post;
use Twig\TwigFunction;

class PostImageExtension extends AbstractExtension
{
    public function __construct(private readonly UrlGeneratorInterface $generator)
    {

    }

    public function getFunctions()
    {
        return [
            new TwigFunction('post_url', [$this, 'getPublicUrl'])
        ];
    }

    public function getPublicUrl(Post $post): string
    {
        return $this->generator->generate('post_image', ['filename' => $post->getImagePath(), '_' => $post->getHash()]);
    }
}
