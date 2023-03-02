<?php

use Illuminate\Support\Facades\Route;
use Sowl\JsonApi\Default\Controller;

Route::get('/{resourceKey}', [Controller::class, 'list'])->name('list');
Route::get('/{resourceKey}/{id}', [Controller::class, 'show'])->name('show');
Route::delete('/{resourceKey}/{id}', [Controller::class, 'remove'])->name('remove');

Route::get('/{resourceKey}/{id}/{relationship}', [Controller::class, 'showRelated'])->name('showRelated');
Route::get('/{resourceKey}/{id}/relationships/{relationship}', [Controller::class, 'showRelationships'])->name('showRelationships');
Route::post('/{resourceKey}/{id}/relationships/{relationship}', [Controller::class, 'createRelationships'])->name('createRelationships');
Route::patch('/{resourceKey}/{id}/relationships/{relationship}', [Controller::class, 'updateRelationships'])->name('updateRelationships');
Route::delete('/{resourceKey}/{id}/relationships/{relationship}', [Controller::class, 'removeRelationships'])->name('removeRelationships');