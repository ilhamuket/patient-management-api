<?php

namespace Database\Seeders;

use App\Models\AccessKey;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AccessKeySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AccessKey::create([
            'key' => Str::random(32),
            'name' => 'Default API Key',
            'is_active' => true,
        ]);
    }
}