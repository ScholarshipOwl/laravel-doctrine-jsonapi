<?php

use Illuminate\Support\Facades\Route;
use Sowl\JsonApi\Default\Controller as DefaultController;

Route::group(['as' => config('jsonapi.routing.rootNamePrefix', 'jsonapi.')], function () {
    /* ---------------------------------------------------------
     * Add any new custom routes here.
     * --------------------------------------------------------- */

    /* ---------------------------------------------------------
     * Default routes will be handled by the default controller.
     * --------------------------------------------------------- */
    Route::group(['as' => 'default.'], function () {
        Route::get('/{resourceType}', [DefaultController::class, 'list'])->name('list');
        Route::post('/{resourceType}', [DefaultController::class, 'create'])->name('create');

        Route::get('/{resourceType}/{id}', [DefaultController::class, 'show'])->name('show');
        Route::patch('/{resourceType}/{id}', [DefaultController::class, 'update'])->name('update');
        Route::delete('/{resourceType}/{id}', [DefaultController::class, 'remove'])->name('remove');

        Route::get('/{resourceType}/{id}/{relationship}', [DefaultController::class, 'showRelated'])->name('showRelated');
        Route::get('/{resourceType}/{id}/relationships/{relationship}', [DefaultController::class, 'showRelationships'])->name('showRelationships');
        Route::post('/{resourceType}/{id}/relationships/{relationship}', [DefaultController::class, 'createRelationships'])->name('createRelationships');
        Route::patch('/{resourceType}/{id}/relationships/{relationship}', [DefaultController::class, 'updateRelationships'])->name('updateRelationships');
        Route::delete('/{resourceType}/{id}/relationships/{relationship}', [DefaultController::class, 'removeRelationships'])->name('removeRelationships');
    });
});
