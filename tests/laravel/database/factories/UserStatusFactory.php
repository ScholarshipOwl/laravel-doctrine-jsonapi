<?php

/** @var LaravelDoctrine\ORM\Testing\Factory $factory */

use Tests\App\Entities\UserStatus;

$factory->define(UserStatus::class, fn () => [
    'id' => UserStatus::ACTIVE,
    'name' => 'Active',
]);

$factory->defineAs(UserStatus::class, 'active', function () {
    return (new UserStatus())
        ->setId(UserStatus::ACTIVE)
        ->setName('Active');
});

$factory->defineAs(UserStatus::class, 'inactive', function () {
    return (new UserStatus())
        ->setId(UserStatus::INACTIVE)
        ->setName('Inactive');
});

$factory->defineAs(UserStatus::class, 'deleted', function () {
    return (new UserStatus())
        ->setId(UserStatus::DELETED)
        ->setName('Deleted');
});
