<?php

use Illuminate\Database\Seeder;
use App\Type;
use Illuminate\Support\Facades\DB;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('types')->insert([
            [
                'type' => 'BAJU DEWASA'
            ],[
                'type' => 'CELANA DEWASA'
            ],[
                'type' => 'CELANA ANAK'
            ],[
                'type' => 'CELANA ANAK'
            ],[
                'type' => 'LAINNYA'
            ]
        ]);
    }
}
