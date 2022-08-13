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
                'type' => 'Baju Anak'
            ],[
                'type' => 'Baju Dewasa'
            ],[
                'type' => 'Celana Anak'
            ],[
                'type' => 'Celana Dewasa'
            ],[
                'type' => 'Lainnya'
            ]
        ]);
    }
}
