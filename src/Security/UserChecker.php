<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return;
        }

        if (false === $user->isVerified()) {
            throw new CustomUserMessageAccountStatusException('Il tuo account non è stato verificato. Controlla la tua email e segui il link di verifica che ti è stato inviato.');
        }

        if (false === $user->isEnabled()) {
            throw new DisabledException('Il tuo account è stato disabilitato.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // non c'è nulla da fare qui
    }

}
