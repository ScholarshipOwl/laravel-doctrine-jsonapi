<?php

use Illuminate\Support\Facades\Route;
use Tests\App\Http\Controller\PageCommentController;
use Tests\App\Http\Controller\PageController;
use Tests\App\Http\Controller\RolesController;
use Tests\App\Http\Controller\UsersController;

use Sowl\JsonApi\Default\Controller as DefaultController;

Route::group(['as' => 'jsonapi.'], function () {
    /* ---------------------------------------------------------
     * Add any new custom routes here.
     * --------------------------------------------------------- */
    Route::get('/pages/{id}', [PageController::class, 'show']);

    Route::patch('/pages/{id}/relationships/user', [PageController::class, 'updateUserRelationship']);

    Route::get('/pageComments/{id}', [PageCommentController::class, 'show']);

    Route::get('/roles', [RolesController::class, 'list']);
    Route::get('/users', [UsersController::class, 'list']);

    Route::post('/users', [UsersController::class, 'create']);
    Route::patch('/users/{id}', [UsersController::class, 'update']);
    Route::post('/users/{id}/relationships/roles', [UsersController::class, 'createUserRoles']);
    Route::delete('/users/{id}/relationships/roles', [UsersController::class, 'removeUserRoles']);
    Route::patch('/users/{id}/relationships/roles', [UsersController::class, 'updateUserRoles']);


    /* ---------------------------------------------------------
     * Default routes will be handled by the default controller.
     * --------------------------------------------------------- */
    Route::group(['as' => 'default.'], function () {
        Route::get('/{resourceKey}', [DefaultController::class, 'list'])->name('list');
        Route::post('/{resourceKey}', [DefaultController::class, 'create'])->name('create');

        Route::get('/{resourceKey}/{id}', [DefaultController::class, 'show'])->name('show');
        Route::patch('/{resourceKey}/{id}', [DefaultController::class, 'update'])->name('update');
        Route::delete('/{resourceKey}/{id}', [DefaultController::class, 'remove'])->name('remove');

        Route::get('/{resourceKey}/{id}/{relationship}', [DefaultController::class, 'showRelated'])->name('showRelated');
        Route::get('/{resourceKey}/{id}/relationships/{relationship}', [DefaultController::class, 'showRelationships'])->name('showRelationships');
        Route::post('/{resourceKey}/{id}/relationships/{relationship}', [DefaultController::class, 'createRelationships'])->name('createRelationships');
        Route::patch('/{resourceKey}/{id}/relationships/{relationship}', [DefaultController::class, 'updateRelationships'])->name('updateRelationships');
        Route::delete('/{resourceKey}/{id}/relationships/{relationship}', [DefaultController::class, 'removeRelationships'])->name('removeRelationships');
    });
});
