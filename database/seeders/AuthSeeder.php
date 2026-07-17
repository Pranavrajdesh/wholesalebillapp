<?php

namespace Database\Seeders;

use App\Models\Partner;
use App\Models\RateGroup;
use App\Models\User;
use Illuminate\Database\Seeder;

class AuthSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::updateOrCreate(
            ['mobile' => '9999999999'],
            ['name' => 'XYZ Wholesale Store']
        );

        $general = RateGroup::firstOrCreate(['name' => 'General']);

        Partner::updateOrCreate(
            ['mobile' => '8888888888'],
            [
                'firm_name' => 'ABC General Store',
                'contact_name' => 'Ramesh',
                'rate_group_id' => $general->id,
                'portal_access' => true,
                'is_active' => true,
            ]
        );

        Partner::updateOrCreate(
            ['mobile' => '7777777777'],
            [
                'firm_name' => 'Kirana Retail Store',
                'contact_name' => 'Suresh',
                'rate_group_id' => $general->id,
                'portal_access' => true,
                'is_active' => true,
            ]
        );

        $this->command->info('Seeded owner ' . $owner->name . ', rate group General, ' . Partner::count() . ' partners.');
    }
}