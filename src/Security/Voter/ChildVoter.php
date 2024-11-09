<?php

namespace App\Security\Voter;


use App\Entity\Child;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ChildVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (false === in_array($attribute, [EntityAction::VIEW->value, EntityAction::EDIT->value])) {
            return false;
        }

        if (false === $subject instanceof Child) {
            return false;
        }
        return true;
    }

    /**
     * @param string $attribute
     * @param Child $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (false === $user instanceof User) {
            return false;
        }

        return $user->getFamilies()->contains($subject->getFamily());
    }

}
