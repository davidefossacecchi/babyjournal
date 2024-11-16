<?php

namespace App\Entity\AuthToken;

enum AuthTokenType: string
{
    case PASSWORD_RESET = 'password_reset';

    case EMAIL_VERIFICATION = 'email_verification';

    case FAMILY_INVITATION = 'family_invitation';
}
