<?php
/** @var LaravelDoctrine\ORM\Testing\Factory $factory */

use Tests\App\Entities\User;
use Tests\App\Entities\Role;
use Doctrine\Common\Collections\ArrayCollection;
use Faker\Generator;

$factory->defineAs(User::class, 'user', function () {
    return [
        'id' => 1,
        'name' => 'testing user1',
        'email' => 'test1email@test.com',
        'password' => 'secret',
        'roles' => new ArrayCollection([
            Role::user(),
        ]),
    ];
});

$factory->defineAs(User::class, 'root', function () {
    return [
        'id' => 2,
        'name' => 'testing user2',
        'email' => 'test2email@gmail.com',
        'password' => 'secret',
        'roles' => new ArrayCollection([
            Role::user(),
            Role::root(),
        ])
    ];
});

$factory->defineAs(User::class, 'moderator', function () {
    return [
        'id' => 2,
        'name' => 'testing user3',
        'email' => 'test3email@test.com',
        'password' => 'secret',
        'roles' => new ArrayCollection([
            Role::user(),
            Role::moderator(),
        ])
    ];
});
