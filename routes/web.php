<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.home');
})->name('home');

Route::get('/projects', function () {
    return view('pages.projects.index');
})->name('projects.index');

Route::get('/styleguide', function () {
    return view('styleguide');
})->name('styleguide');
