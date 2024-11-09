<?php

namespace App\Security\Voter;

use App\Entity\Family;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class FamilyVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (false === in_array($attribute, [EntityAction::VIEW->value, EntityAction::EDIT->value])) {
            return false;
        }

        if (false === $subject instanceof Family) {
            return false;
        }
        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (false === $user instanceof User) {
            return false;
        }

        /** @var Family $subject */
        return $subject->getUsers()->contains($user);
    }
}
