<?php
/** @var LaravelDoctrine\ORM\Testing\Factory $factory */

use Tests\App\Entities\Page;
use Faker\Generator;

$factory->define(Page::class, function (Generator $faker, array $attributes) {
    return [
        'user' => $attributes['user'],
        'title' => $attributes['title'] ?? $faker->sentence(),
        'content' => $attributes['content'] ?? $faker->paragraphs(),
    ];
});

