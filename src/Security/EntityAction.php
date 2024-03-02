<?php

namespace App\Security;

enum EntityAction: string
{
    case VIEW = 'view';
    case EDIT = 'edit';
}
