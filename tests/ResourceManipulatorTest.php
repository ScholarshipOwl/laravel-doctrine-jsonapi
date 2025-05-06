<?php

namespace Tests;

use App\Entities\Role;
use App\Entities\User;
use Sowl\JsonApi\Exceptions\JsonApiException;
use Sowl\JsonApi\ResourceManipulator;

class ResourceManipulatorTest extends TestCase
{
    public function testHydrateAttributesAndRelationships(): void
    {
        /** @var User $user */
        $user = $this->manipulator()->hydrateResource(new User(), [
            'attributes' => [
                'name' => 'TestName',
                'email' => 'test@test.com',
            ],
            'relationships' => [
                'roles' => [
                    'data' => [
                        ['type' => 'roles', 'id' => 2],
                    ],
                ],
            ],
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('TestName', $user->getName());
        $this->assertEquals('test@test.com', $user->getEmail());
        $this->assertTrue($user->hasRoleByName(Role::USER_NAME));
    }

    public function testHydrateExceptions(): void
    {
        try {
            $this->manipulator()->hydrateResource(new User(), ['attributes' => ['not_exists' => 1]]);
            $this->fail('Exception should be thrown.');
        } catch (JsonApiException $e) {
            $this->assertEquals([
                [
                    'code' => 400,
                    'source' => ['pointer' => '/data/attributes/not_exists'],
                    'detail' => 'Unknown attribute.',
                ],
                [
                    'code' => 400,
                    'source' => ['setter' => 'App\Entities\User::setNot_exists'],
                    'detail' => 'Missing property setter.',
                ],
            ], $e->errors());
        }

        try {
            $this->manipulator()->hydrateResource(new User(), ['relationships' => ['not_exists' => 1]]);
            $this->fail('Exception should be thrown.');
        } catch (JsonApiException $e) {
            $this->assertEquals([
                [
                    'code' => '400',
                    'source' => ['pointer' => '/data/relationships/not_exists'],
                    'detail' => 'Unknown relationship.',
                ],
            ], $e->errors());
        }

        try {
            $this->manipulator()->hydrateResource(new User(), ['relationships' => ['roles' => 1]]);
            $this->fail('Exception should be thrown.');
        } catch (JsonApiException $e) {
            $this->assertEquals([
                [
                    'code' => 400,
                    'source' => ['pointer' => '/data/relationships/roles'],
                    'detail' => 'Data is missing or not an array.',
                ],
            ], $e->errors());
        }

        try {
            $this->manipulator()->setProperty(new User(), 'not_exists', 'test');
            $this->fail('Exception should be thrown.');
        } catch (JsonApiException $e) {
            $this->assertEquals([
                [
                    'code' => '400',
                    'source' => [
                        'setter' => User::class . '::setNot_exists',
                    ],
                    'detail' => 'Missing property setter.',
                ],
            ], $e->errors());
        }
    }

    public function testCreateResource(): void
    {
        $user = $this->manipulator()->createResource('users');

        $this->assertInstanceOf(User::class, $user);

        $userWithId = $this->manipulator()->createResource('users', 'test');

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('test', $userWithId->getId());
    }

    protected function manipulator(): ResourceManipulator
    {
        return app(ResourceManipulator::class);
    }
}
