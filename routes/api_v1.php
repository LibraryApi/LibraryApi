<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use app\Http\Controllers\Api\V1\BookController;

Route::get('/books', 'App\Http\Controllers\Api\V1\BookController@index');
Route::get('/books/{id}', 'App\Http\Controllers\Api\V1\BookController@show');
Route::post('/books','App\Http\Controllers\Api\V1\BookController@store');
Route::patch('/books/{id}','App\Http\Controllers\Api\V1\BookController@update');
Route::delete('/books/{id}', 'App\Http\Controllers\Api\V1\BookController@destroy');


Route::get('posts', 'App\Http\Controllers\Api\V1\PostController@index');
Route::get('posts/{id}', 'App\Http\Controllers\Api\V1\PostController@show');

Route::get('comments', 'App\Http\Controllers\Api\V1\CommentController@index');
Route::get('comments/{id}', 'App\Http\Controllers\Api\V1\CommentController@show');

Route::get('users', 'App\Http\Controllers\Api\V1\UserController@index');
Route::get('users/{id}', 'App\Http\Controllers\Api\V1\UserController@show');