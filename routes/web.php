<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::user.home')->name('home');
Route::livewire('/category/{category:slug}', 'pages::user.articles')->name('articles');
Route::livewire('/search', 'pages::user.search')->name('search');
Route::livewire('/article/{article:slug}', 'pages::user.article')->name('article.show');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::livewire('/login', 'pages::admin.login')->name('login');
    Route::livewire('/dashboard', 'pages::admin.dashboard')->name('dashboard');
});