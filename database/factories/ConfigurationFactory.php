<?php

use Faker\Generator as Faker;
use App\Model;

$factory->define(Model\Configuration::class, function (Faker $faker) {
    return [
        'title' => $faker->text(200),
        'config_key' => $faker->text(200),
        'config_value' => $faker->text(200),
    ];
});
