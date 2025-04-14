<?php

/** @var LaravelDoctrine\ORM\Testing\Factory $factory */

use Tests\App\Entities\User;
use Tests\App\Entities\UserStatus;
use Tests\App\Entities\Role;
use Doctrine\Common\Collections\ArrayCollection;

$factory->define(User::class, function (Faker\Generator $faker) use ($factory) {
    return [
        'id' => $faker->uuid,
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => 'secret',
        'status' => fn () => entity(UserStatus::class, 'active')->make(),
//        'roles' => new ArrayCollection([
//            $factory->makeAs(Role::class, Role::USER_NAME)
//        ]),
    ];
});

$factory->defineAs(User::class, 'user', function (Faker\Generator $faker) {
    return [
        'id' => User::USER_ID,
        'name' => 'testing user1',
        'email' => 'test1email@test.com',
        'password' => 'secret',
        'status' => fn () => entity(UserStatus::class, 'active')->make(),
        'roles' => new ArrayCollection([
            Role::user(),
        ]),
    ];
});

$factory->defineAs(User::class, 'root', function () {
    return [
        'id' => User::ROOT_ID,
        'name' => 'testing user2',
        'email' => 'test2email@gmail.com',
        'password' => 'secret',
        'status' => fn () => entity(UserStatus::class, 'active')->make(),
        'roles' => new ArrayCollection([
            Role::user(),
            Role::root(),
        ])
    ];
});

$factory->defineAs(User::class, 'moderator', function () {
    return [
        'id' => User::MODERATOR_ID,
        'name' => 'testing user3',
        'email' => 'test3email@test.com',
        'password' => 'secret',
        'status' => fn () => entity(UserStatus::class, 'active')->make(),
        'roles' => new ArrayCollection([
            Role::user(),
            Role::moderator(),
        ])
    ];
});
