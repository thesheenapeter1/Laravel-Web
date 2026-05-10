<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->orderBy('id')->lazy()->each(function ($user) {
            $pw = $user->password;
            if (! is_string($pw) || $pw === '') {
                return;
            }
            // Bcrypt hashes start with $2y$ / $2a$ / $2b$. Argon with $argon2.
            if (preg_match('/^\$(2[aby]|argon2)/', $pw)) {
                return;
            }
            DB::table('users')->where('id', $user->id)->update([
                'password' => Hash::make($pw),
            ]);
        });
    }

    public function down(): void
    {
        // No-op: we can't un-hash a password.
    }
};
