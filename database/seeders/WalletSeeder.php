<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class WalletSeeder extends Seeder
{
    public function run(): void
    {
        // Akun mahasiswa pertama
        $user1 = User::firstOrCreate(
            ['email' => 'desta@dompetku.test'],
            [
                'name' => 'Desta',
                'password' => Hash::make('password123'),
            ]
        );

        // Akun mahasiswa kedua
        $user2 = User::firstOrCreate(
            ['email' => 'teman@dompetku.test'],
            [
                'name' => 'Teman Desta',
                'password' => Hash::make('password123'),
            ]
        );

        // Membuat dompet dengan saldo awal
        Wallet::firstOrCreate(
            ['user_id' => $user1->id],
            ['balance' => 100000]
        );

        Wallet::firstOrCreate(
            ['user_id' => $user2->id],
            ['balance' => 50000]
        );
    }
}
