<?php

use App\Models\UserProfile;
use Faker\Generator as Faker;

$factory->define(UserProfile::class, function (Faker $faker) {
    return [
        'first_name' => $faker->firstName,
        'image' => $faker->imageUrl(),
        'last_name' => $faker->lastName,
        'user_id' => $faker->randomDigit,
    ];
});