<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed a known development user.
     *
     * Credentials (for local development only — never use in production):
     *   email:    dev@techword.local
     *   password: DevTechWord2026!
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'dev@techword.local'],
            [
                'name'     => 'Dev User',
                'password' => Hash::make('DevTechWord2026!'),
            ]
        );
    }
}
