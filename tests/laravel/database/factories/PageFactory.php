<?php

/** @var LaravelDoctrine\ORM\Testing\Factory $factory */

use Tests\App\Entities\Page;
use Tests\App\Entities\User;
use Faker\Generator;

$factory->define(Page::class, function (Generator $faker, array $attributes) use ($factory) {
    return [
        'user' => $attributes['user'] ?? $factory->of(User::class)->create(),
        'title' => $attributes['title'] ?? $faker->sentence(),
        'content' => $attributes['content'] ?? join("\n", $faker->paragraphs()),
    ];
});
