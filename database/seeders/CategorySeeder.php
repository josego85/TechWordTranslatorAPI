<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['slug' => 'networking',             'name' => 'Networking'],
            ['slug' => 'databases',              'name' => 'Databases'],
            ['slug' => 'security',               'name' => 'Security'],
            ['slug' => 'algorithms',             'name' => 'Algorithms'],
            ['slug' => 'data-structures',        'name' => 'Data Structures'],
            ['slug' => 'operating-systems',      'name' => 'Operating Systems'],
            ['slug' => 'programming-languages',  'name' => 'Programming Languages'],
            ['slug' => 'web',                    'name' => 'Web'],
            ['slug' => 'cloud',                  'name' => 'Cloud'],
            ['slug' => 'devops',                 'name' => 'DevOps'],
            ['slug' => 'hardware',               'name' => 'Hardware'],
            ['slug' => 'artificial-intelligence', 'name' => 'Artificial Intelligence'],
            ['slug' => 'other',                  'name' => 'Other'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
