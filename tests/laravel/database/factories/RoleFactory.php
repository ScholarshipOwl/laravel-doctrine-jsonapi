<?php
/** @var LaravelDoctrine\ORM\Testing\Factory $factory */

use Tests\App\Entities\Role;

$roles = [
    Role::ROOT => Role::ROOT_NAME,
    Role::USER => Role::USER_NAME,
    Role::MODERATOR => Role::MODERATOR_NAME,
];

foreach ($roles as $id => $name) {
    $factory->defineAs(Role::class, $name, fn () => [
        'id' => $id,
        'name' => $name,
    ]);
}
