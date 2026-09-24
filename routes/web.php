<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::user.home')->name('home');
Route::livewire('/category/{category:slug}', 'pages::user.articles')->name('articles');
Route::livewire('/category/{article:slug}', 'pages::user.article')->name('article');
