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

        return $subject instanceof Child;
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

        return $user instanceof User && $user->getFamilies()->contains($subject->getFamily());
    }

}
