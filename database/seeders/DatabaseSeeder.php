<?php

namespace Database\Seeders;

use App\Models\Folder;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // DatabaseSeeder.php
        $systemFolders = [
            ['name' => 'Вхідні', 'slug' => 'inbox', 'is_system' => true],
            ['name' => 'Надіслані', 'slug' => 'sent', 'is_system' => true],
            ['name' => 'Видалені', 'slug' => 'trash', 'is_system' => true],
            ['name' => 'Чернетки', 'slug' => 'drafts', 'is_system' => true],
            ['name' => 'Спам', 'slug' => 'spam', 'is_system' => true],
        ];

        foreach ($systemFolders as $folder) {
            Folder::firstOrCreate(
                ['slug' => $folder['slug']],
                $folder
            );
        }


        $this->call([
            MailTestSeeder::class,
        ]);
    }
}
