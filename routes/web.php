<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::user.home')->name('home');
Route::livewire('/category/{category:slug}', 'pages::user.articles')->name('articles');
Route::livewire('/search', 'pages::user.search')->name('search');
Route::livewire('/article/{article:slug}', 'pages::user.article')->name('article.show');
Route::livewire('/article/{article:slug}', 'pages::user.article')->name('article.show');
Route::livewire('/about-us', 'pages::user.about-me')->name('about-me');

Route::post('/logout', [UserController::class, 'logout'])->name('logout');
Route::livewire('/admin/login', 'pages::admin.login')->middleware('guest')->name('login');

Route::middleware(['admin', 'auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::livewire('/dashboard', 'pages::admin.dashboard')->name('dashboard');
    Route::livewire('/dashboard/article', 'pages::admin.article')->name('articles.index');
    Route::livewire('/dashboard/article/add', 'pages::admin.add-article')->name('articles.add');
    Route::livewire('/dashboard/categories', 'pages::admin.categories')->name('categories.index');
});