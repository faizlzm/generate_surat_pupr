<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use Illuminate\Database\Seeder;

class AppSettingSeeder extends Seeder
{
    public function run(): void
    {
        AppSetting::query()->updateOrCreate(
            ['key' => 'general'],
            ['value_json' => AppSetting::defaultGeneral()]
        );
    }
}
