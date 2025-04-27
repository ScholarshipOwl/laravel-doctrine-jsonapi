<?php

namespace Tests\Action\Relationships\ToOne;

use Sowl\JsonApi\Response;
use Tests\App\Entities\User;
use Tests\TestCase;

class ShowRelationshipTest extends TestCase
{
    public function test_authorization_permissions_any_one_can_access()
    {
        $this->actingAsUser();
        $this->get('/pageComments/00000000-0000-0000-0000-000000000001/relationships/user')->assertStatus(Response::HTTP_OK);
        $this->get('/pageComments/00000000-0000-0000-0000-000000000001/relationships/page')->assertStatus(Response::HTTP_OK);
        $this->get('/pageComments/00000000-0000-0000-0000-000000000002/relationships/user')->assertStatus(Response::HTTP_OK);
        $this->get('/pageComments/00000000-0000-0000-0000-000000000002/relationships/page')->assertStatus(Response::HTTP_OK);
        $this->get('/pageComments/00000000-0000-0000-0000-000000000003/relationships/user')->assertStatus(Response::HTTP_OK);
        $this->get('/pageComments/00000000-0000-0000-0000-000000000003/relationships/page')->assertStatus(Response::HTTP_OK);
    }

    public function test_show_page_comments_related_user_response()
    {
        $this->actingAsUser();
        $this->get('/pageComments/00000000-0000-0000-0000-000000000001/relationships/user')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => User::USER_ID,
                    'type' => 'users',
                    'links' => [
                        'self' => '/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b',
                    ],
                ],
            ]);

        $this->get('/pageComments/00000000-0000-0000-0000-000000000002/relationships/user')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => User::ROOT_ID,
                    'type' => 'users',
                    'links' => [
                        'self' => '/users/f1d2f365-e9aa-4844-8eb7-36e0df7a396d',
                    ],
                ],
            ]);

        $this->get('/pageComments/00000000-0000-0000-0000-000000000003/relationships/user')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => User::MODERATOR_ID,
                    'type' => 'users',
                    'links' => [
                        'self' => '/users/ccf660b9-3cf7-4f58-a5f7-22e53ad836f8',
                    ],
                ],
            ]);
    }

    public function test_show_page_comments_related_page()
    {
        $this->actingAsUser();
        $page1response = [
            'data' => [
                'id' => '1',
                'type' => 'pages',
                'links' => [
                    'self' => '/pages/1',
                ],
            ],
        ];

        $this->get('/pageComments/00000000-0000-0000-0000-000000000001/relationships/page')
            ->assertStatus(200)
            ->assertExactJson($page1response);

        $this->get('/pageComments/00000000-0000-0000-0000-000000000002/relationships/page')
            ->assertStatus(200)
            ->assertExactJson($page1response);

        $this->get('/pageComments/00000000-0000-0000-0000-000000000003/relationships/page')
            ->assertStatus(200)
            ->assertExactJson($page1response);
    }
}
