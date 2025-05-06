<?php

use Illuminate\Support\Facades\Route;
use Sowl\JsonApi\Default\Controller as DefaultController;

Route::group(['as' => config('jsonapi.routing.name', 'jsonapi.')], function () {
    /* ---------------------------------------------------------
     * Add any new custom routes here.
     * --------------------------------------------------------- */

    /* ---------------------------------------------------------
     * Default routes will be handled by the default controller.
     * --------------------------------------------------------- */
    Route::prefix('{resourceType}')->name('default.')->group(function () {
        Route::get('/', [DefaultController::class, 'list'])->name('list')->fallback();
        Route::post('/', [DefaultController::class, 'create'])->name('create')->fallback();

        Route::get('{id}', [DefaultController::class, 'show'])->name('show')->fallback();
        Route::patch('{id}', [DefaultController::class, 'update'])->name('update')->fallback();
        Route::delete('{id}', [DefaultController::class, 'remove'])->name('remove')->fallback();

        Route::get('{id}/{relationship}', [DefaultController::class, 'showRelated'])->name('showRelated')->fallback();
        Route::prefix('{id}/relationships/{relationship}')->group(function () {
            Route::get('/', [DefaultController::class, 'showRelationships'])->name('showRelationships')->fallback();
            Route::post('/', [DefaultController::class, 'createRelationships'])->name('createRelationships')->fallback();
            Route::patch('/', [DefaultController::class, 'updateRelationships'])->name('updateRelationships')->fallback();
            Route::delete('/', [DefaultController::class, 'removeRelationships'])->name('removeRelationships')->fallback();
        });
    });
});
