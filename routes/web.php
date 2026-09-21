<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::user.home')->name('home');
Route::livewire('/articles/', 'pages::user.articles')->name('articles');

