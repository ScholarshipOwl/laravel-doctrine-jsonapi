<?php

namespace Database\Seeders;

use App\Entities\Page;
use App\Entities\PageComment;
use App\Entities\Role;
use App\Entities\User;
use App\Entities\UserStatus;
use Illuminate\Database\Seeder;

class SetUpSeeder extends Seeder
{
    public function run(): void
    {
        entity(UserStatus::class, 'active')->create();
        entity(UserStatus::class, 'inactive')->create();
        entity(UserStatus::class, 'deleted')->create();

        entity(Role::class, Role::ROOT_NAME)->create();
        entity(Role::class, Role::USER_NAME)->create();
        entity(Role::class, Role::MODERATOR_NAME)->create();

        $user = entity(User::class, 'user')->create();
        $root = entity(User::class, 'root')->create();
        $moderator = entity(User::class, 'moderator')->create();

        $page = entity(Page::class)->create([
            'user' => $user,
            'title' => 'JSON:API standard',
            'content' => '<strong>JSON:API</strong>',
        ]);

        entity(PageComment::class)->create([
            'id' => PageComment::FIRST,
            'page' => $page,
            'user' => $user,
            'content' => '<span>It is mine comment</span>',
        ]);

        entity(PageComment::class)->create([
            'id' => PageComment::SECOND,
            'page' => $page,
            'user' => $root,
            'content' => '<span>I know better</span>',
        ]);

        entity(PageComment::class)->create([
            'id' => PageComment::THIRD,
            'page' => $page,
            'user' => $moderator,
            'content' => '<span>I think he is right</span>',
        ]);
    }
}
