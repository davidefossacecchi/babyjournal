<?php

namespace App\Security\Voter;

enum EntityAction: string
{
    case VIEW = 'view';
    case EDIT = 'edit';
}
