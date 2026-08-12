<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.home');
})->name('home');

Route::get('/projects', function () {
    return view('pages.projects.index');
})->name('projects.index');

Route::get('/services', function () {
    return view('pages.services.index');
})->name('services.index');

Route::get('/about', function () {
    return view('pages.about.index');
})->name('about');

Route::get('/contact', function () {
    return view('pages.contact.index');
})->name('contact');

Route::get('/styleguide', function () {
    return view('styleguide');
})->name('styleguide');
