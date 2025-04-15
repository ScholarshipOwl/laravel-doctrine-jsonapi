<?php

/** @var LaravelDoctrine\ORM\Testing\Factory $factory */

use Tests\App\Entities\User;
use Tests\App\Entities\UserStatus;
use Tests\App\Entities\Role;
use Tests\App\Entities\UserConfig;
use Doctrine\Common\Collections\ArrayCollection;

use LaravelDoctrine\ORM\Facades\EntityManager;

$factory->define(User::class, function (Faker\Generator $faker) use ($factory) {
    return [
        'id' => $faker->uuid,
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => 'secret',
        'status' => UserStatus::active(),
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
        'status' => UserStatus::active(),
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
        'status' => UserStatus::active(),
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
        'status' => UserStatus::active(),
        'roles' => new ArrayCollection([
            Role::user(),
            Role::moderator(),
        ])
    ];
});

$factory->afterMaking(User::class, function (User $user, \Faker\Generator $faker) {
    $config = new UserConfig();
    $config->setUser($user);
    $config->setTheme('light');
    $config->setNotificationsEnabled(true);
    $config->setLanguage('en');
    $user->setConfig($config);
});
