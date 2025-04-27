<?php

/** @var LaravelDoctrine\ORM\Testing\Factory $factory */

use App\Entities\Role;

$factory->define(Role::class, fn () => [
    'id' => Role::USER,
    'name' => Role::USER_NAME,
]);

$roles = [
    Role::ROOT => Role::ROOT_NAME,
    Role::USER => Role::USER_NAME,
    Role::MODERATOR => Role::MODERATOR_NAME,
];

foreach ($roles as $id => $name) {
    $factory->defineAs(Role::class, $name, fn () => (new Role)->setId($id)->setName($name));
}
