<?php

use Illuminate\Support\Facades\Route;
use Tests\App\Http\Controller\PageCommentController;
use Tests\App\Http\Controller\PageController;
use Tests\App\Http\Controller\RolesController;
use Tests\App\Http\Controller\UsersController;

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
