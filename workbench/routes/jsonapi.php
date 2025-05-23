<?php

use App\Http\Controller\PageComment\PageCommentController;
use App\Http\Controller\Pages\PageController;
use App\Http\Controller\Roles\RolesController;
use App\Http\Controller\Users\UsersController;
use Illuminate\Support\Facades\Route;
use Sowl\JsonApi\Default\Controller as DefaultController;

Route::group(['as' => config('jsonapi.routing.name', 'jsonapi.')], function () {
    /* ---------------------------------------------------------
     * Add any new custom routes here.
     * --------------------------------------------------------- */
    Route::get('/pages/{id}', [PageController::class, 'show']);

    Route::patch('/pages/{id}/relationships/user', [PageController::class, 'updateUserRelationship']);

    Route::get('/pageComments/{id}', [PageCommentController::class, 'show']);
    Route::get('/pageComments/{id}/{relationship}', [PageCommentController::class, 'showRelated']);
    Route::get('/pageComments/{id}/relationships/{relationship}', [PageCommentController::class, 'showRelationships']);

    Route::get('/roles', [RolesController::class, 'list']);

    Route::group(['prefix' => '/users', 'as' => 'users.'], function () {
        Route::post('/', [UsersController::class, 'create'])->name('create');
        Route::patch('/{id}', [UsersController::class, 'update'])->name('update');
        Route::post('/{id}/relationships/roles', [UsersController::class, 'createUserRoles'])->name('createUserRoles');
        Route::delete('/{id}/relationships/roles', [UsersController::class, 'removeUserRoles'])->name('removeUserRoles');
        Route::patch('/{id}/relationships/roles', [UsersController::class, 'updateUserRoles'])->name('updateUserRoles');
    });

    /* ---------------------------------------------------------
     * Default routes will be handled by the default controller.
     * --------------------------------------------------------- */
    Route::group(['as' => 'default.'], function () {
        Route::get('/{resourceType}', [DefaultController::class, 'list'])->name('list')->fallback();
        Route::post('/{resourceType}', [DefaultController::class, 'create'])->name('create')->fallback();

        Route::get('/{resourceType}/{id}', [DefaultController::class, 'show'])->name('show')->fallback();
        Route::patch('/{resourceType}/{id}', [DefaultController::class, 'update'])->name('update')->fallback();
        Route::delete('/{resourceType}/{id}', [DefaultController::class, 'remove'])->name('remove')->fallback();

        Route::get('/{resourceType}/{id}/{relationship}', [DefaultController::class, 'showRelated'])->name('showRelated')->fallback();
        Route::get('/{resourceType}/{id}/relationships/{relationship}', [DefaultController::class, 'showRelationships'])->name('showRelationships')->fallback();
        Route::post('/{resourceType}/{id}/relationships/{relationship}', [DefaultController::class, 'createRelationships'])->name('createRelationships')->fallback();
        Route::patch('/{resourceType}/{id}/relationships/{relationship}', [DefaultController::class, 'updateRelationships'])->name('updateRelationships')->fallback();
        Route::delete('/{resourceType}/{id}/relationships/{relationship}', [DefaultController::class, 'removeRelationships'])->name('removeRelationships')->fallback();
    });

});
