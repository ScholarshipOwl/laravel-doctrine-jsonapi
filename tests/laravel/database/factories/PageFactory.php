<?php

/** @var LaravelDoctrine\ORM\Testing\Factory $factory */

use Tests\App\Entities\Page;
use Tests\App\Entities\User;

$factory->define(Page::class, function (Faker\Generator $faker, array $attributes) use ($factory) {
    /** @var User $author */
    $author = $attributes['user'] ?? $factory->of(User::class)->create();

    return (new Page)
        ->setTitle($attributes['title'] ?? $faker->sentence())
        ->setContent($attributes['content'] ?? implode("\n", $faker->paragraphs()))
        ->setUser($author);
});
