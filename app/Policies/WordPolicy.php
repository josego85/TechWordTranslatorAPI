<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class WordPolicy
{
    public function write(User $user): bool
    {
        /** @var mixed $token */
        $token = $user->currentAccessToken();

        return $token === null || $user->tokenCan('words:write');
    }
}
