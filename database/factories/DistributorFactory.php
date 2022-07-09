<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Distributor;
use Faker\Generator as Faker;

$factory->define(Distributor::class, function (Faker $faker) {
    static $phone = 62812512371;
    return [
        'name' => $faker->name(),
        'phone' => $phone++,
        'db_id' => 0
    ];
});
