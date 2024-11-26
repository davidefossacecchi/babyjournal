<?php

namespace App\Entity\AuthToken;

interface UserInvitationTokenInterface
{
    public function getEmail(): string;

    public function setEmail(string $email): static;
}
