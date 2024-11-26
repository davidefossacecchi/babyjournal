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

        return $subject instanceof Family;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (false === $user instanceof User) {
            return false;
        }

        return match ($attribute) {
            EntityAction::VIEW->value => $this->canUserViewFamily($user, $subject),
            EntityAction::EDIT->value => $this->canUserEditFamily($user, $subject),
            default => false,
        };

    }

    protected function canUserViewFamily(User $user, Family $family): bool
    {
        $representedChildren = $user->getRepresentedChildren();
        // if the user can edit the family, they can view it
        if ($this->canUserEditFamily($user, $family)) {
            return true;
        }

        // if a user represents a child, they can view the family
        foreach ($representedChildren as $child) {
            if ($family->getChildren()->contains($child)) {
                return true;
            }
        }
        return false;
    }

    protected function canUserEditFamily(User $user, Family $family): bool
    {
        return $family->getUsers()->contains($user);
    }
}
