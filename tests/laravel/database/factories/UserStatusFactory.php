<?php

/** @var LaravelDoctrine\ORM\Testing\Factory $factory */

use Tests\App\Entities\UserStatus;

$factory->defineAs(UserStatus::class, 'active', function () {
    return [
        'id' => UserStatus::ACTIVE,
        'name' => 'Active',
    ];
});

$factory->defineAs(UserStatus::class, 'inactive', function () {
    return [
        'id' => UserStatus::INACTIVE,
        'name' => 'Inactive',
    ];
});

$factory->defineAs(UserStatus::class, 'deleted', function () {
    return [
        'id' => UserStatus::DELETED,
        'name' => 'Deleted',
    ];
});
