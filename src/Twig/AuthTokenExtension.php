<?php

namespace App\Twig;

use App\Entity\AuthToken;
use App\Serializer\AuthTokenSerializer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AuthTokenExtension extends AbstractExtension
{
    public function __construct(private AuthTokenSerializer $serializer)
    {

    }

    public function getFunctions()
    {
        return [
            new TwigFunction('serialize_token', [$this, 'serializeToken'])
        ];
    }

    public function serializeToken(AuthToken $token): string
    {
        return $this->serializer->serialize($token);
    }
}
