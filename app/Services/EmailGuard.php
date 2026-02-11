<?php

namespace App\Services;

class EmailGuard
{
    private const SEED_DOMAIN = '@seed.partyhelp.com.au';

    public static function shouldSendTo(string $email): bool
    {
        $email = trim($email);
        if ($email === '') {
            return false;
        }

        return ! str_contains(strtolower($email), self::SEED_DOMAIN);
    }
}
