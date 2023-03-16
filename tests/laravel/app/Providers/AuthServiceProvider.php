<?php

namespace Tests\App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Tests\App\Entities\User;
use Tests\App\Entities\Role;
use Tests\App\Entities\Page;
use Tests\App\Entities\PageComment;
use Tests\App\Policies\PageCommentPolicy;
use Tests\App\Policies\PagePolicy;
use Tests\App\Policies\RolePolicy;
use Tests\App\Policies\UserPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The entity to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Role::class => RolePolicy::class,
        Page::class => PagePolicy::class,
        PageComment::class => PageCommentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::guessPolicyNamesUsing(fn () => false);

        Gate::before(function($user, $ability) {
            if ($user instanceof User && $user->isRoot()) {
                return true;
            }
        });
    }
}
