<?php

use Illuminate\Database\Seeder;
use App\PartnerGroup;
use Illuminate\Support\Facades\DB;

class PartnerGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('partner_groups')->insert([
            [
                'prtnr_code' => 'DB',
                'prtnr_name' => 'Distributor',
                'prtnr_desc' => 'highest role',
                'discount' => 40,
            ],[
                'prtnr_code' => 'MS',
                'prtnr_name' => 'Mutif Store',
                'prtnr_desc' => 'Second role',
                'discount' => 35,
            ]
        ]);
    }
}
