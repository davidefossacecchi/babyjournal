<?php

namespace App\Twig;

use App\Entity\Timepoints\Post;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PostImageExtension extends AbstractExtension
{
    public function __construct(private readonly UrlGeneratorInterface $generator)
    {

    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('post_url', [$this, 'getPublicUrl'])
        ];
    }

    public function getPublicUrl(Post $post): string
    {
        return $this->generator->generate('post_image', ['filename' => $post->getImagePath(), 'hash' => $post->getHash()]);
    }
}
