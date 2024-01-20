<?php

namespace App\Serializer;


use App\Entity\AuthToken;

class AuthTokenSerializer
{
    public function serialize(AuthToken $token): string
    {
        $plainVerifier = $token->getPlainVerifier();
        if (empty($plainVerifier)) {
            throw new \InvalidArgumentException('Plain verifier is not set');
        }

        return $token->getSelector().'.'.$token->getPlainVerifier();
    }

    public function deserialize(string $serializedToken, AuthToken $token): AuthToken
    {
        $parts = explode('.', $serializedToken);

        if (count($parts) !== 2) {
            throw new \InvalidArgumentException('Invalid token format');
        }
        $token->setSelector($parts[0]);
        $token->setPlainVerifier($parts[1]);
        return $token;
    }
}
