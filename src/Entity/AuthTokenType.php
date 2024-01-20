<?php

namespace App\Entity;

enum AuthTokenType: string
{
    case PASSWORD_RESET = 'password_reset';
}
