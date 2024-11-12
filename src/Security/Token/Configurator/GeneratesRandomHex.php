<?php

namespace App\Security\Token\Configurator;

trait GeneratesRandomHex
{
    public function getRandomHex(int $bytesLength = 16): string
    {
        return bin2hex(random_bytes($bytesLength));
    }
}
