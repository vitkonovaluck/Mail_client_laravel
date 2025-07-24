<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\VirtualAlias;
use App\Models\VirtualDomain;
use App\Models\VirtualUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MailTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $domain = VirtualDomain::create(['name' => 'ce.vn.ua']);

        VirtualUser::create([
            'domain_id' => $domain->id,
            'email' => 'admin@ce.vn.ua',
            'password' => '{SHA512-CRYPT}' . crypt('informix', '$6$rounds=5000$somesalt$'),
        ]);

        User::create([
            'name' => 'Admin',
            'email' => 'admin@ce.vn.ua',
            'password' => Hash::make('informix',),
        ]);

        VirtualAlias::create([
            'source' => 'contact@ce.vn.ua',
            'destination' => 'admin@ce.vn.ua',
        ]);

        $domain = VirtualDomain::create(['name' => 'microcode.vn.ua']);

        VirtualUser::create([
            'domain_id' => $domain->id,
            'email' => 'admin@microcode.vn.ua',
            'password' => '{SHA512-CRYPT}' . crypt('informix', '$6$rounds=5000$somesalt$'),
        ]);

        User::create([
            'name' => 'Admin',
            'email' => 'admin@microcode.vn.ua',
            'password' => Hash::make('informix',),
        ]);

        VirtualAlias::create([
            'source' => 'contact@microcode.vn.ua',
            'destination' => 'admin@microcode.vn.ua',
        ]);    }
}
