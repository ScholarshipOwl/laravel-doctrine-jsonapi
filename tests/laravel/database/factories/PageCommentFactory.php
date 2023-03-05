<?php
/** @var LaravelDoctrine\ORM\Testing\Factory $factory */

use Tests\App\Entities\User;
use Tests\App\Entities\Role;
use Doctrine\Common\Collections\ArrayCollection;
use Tests\App\Entities\PageComment;
use Faker\Generator;

$factory->define(PageComment::class, function (Generator $faker, array $attributes) {
    return [
        'user' => $attributes['user'],
        'page' => $attributes['page'],
        'content' => $attributes['content'] ?? $faker->paragraphs(1),
    ];
});