<?php

/** @var LaravelDoctrine\ORM\Testing\Factory $factory */

use App\Entities\Role;
use App\Entities\User;
use App\Entities\UserConfig;
use App\Entities\UserStatus;
use Doctrine\Common\Collections\ArrayCollection;

$factory->define(User::class, function (Faker\Generator $faker) {
    return [
        'id' => $faker->uuid,
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => 'secret',
        'status' => UserStatus::active(),
    ];
});

$factory->afterCreating(User::class, function (User $user, \Faker\Generator $faker) {
    $config = new UserConfig;
    $config->setUser($user);
    $config->setTheme('light');
    $config->setNotificationsEnabled(true);
    $config->setLanguage('en');
    $user->setConfig($config);
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

$factory->afterCreating(User::class, function (User $user, \Faker\Generator $faker) {
    $config = new UserConfig;
    $config->setUser($user);
    $config->setTheme('light');
    $config->setNotificationsEnabled(true);
    $config->setLanguage('en');
    $user->setConfig($config);
}, 'user');

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
        ]),
    ];
});

$factory->afterCreating(User::class, function (User $user, \Faker\Generator $faker) {
    $config = new UserConfig;
    $config->setUser($user);
    $config->setTheme('light');
    $config->setNotificationsEnabled(true);
    $config->setLanguage('en');
    $user->setConfig($config);
}, 'root');

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
        ]),
    ];
});

$factory->afterCreating(User::class, function (User $user, \Faker\Generator $faker) {
    $config = new UserConfig;
    $config->setUser($user);
    $config->setTheme('light');
    $config->setNotificationsEnabled(true);
    $config->setLanguage('en');
    $user->setConfig($config);
}, 'moderator');
