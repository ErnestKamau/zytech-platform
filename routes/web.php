<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.home');
})->name('home');

Route::get('/projects', function () {
    return view('pages.projects.index');
})->name('projects.index');

Route::get('/projects/category/{category}', function (string $category) {
    return view('pages.projects.index', ['category' => $category]);
})->name('projects.category');

Route::get('/projects/{slug}', function (string $slug) {
    return view('pages.projects.show', ['slug' => $slug]);
})->name('projects.show');

Route::get('/services', function () {
    return view('pages.services.index');
})->name('services.index');

Route::get('/services/category/{category}', function (string $category) {
    return view('pages.services.index', ['category' => $category]);
})->name('services.category');

Route::get('/services/{slug}', function (string $slug) {
    return view('pages.services.show', ['slug' => $slug]);
})->name('services.show');

Route::get('/knowledge', function () {
    return view('pages.knowledge.index');
})->name('knowledge.index');

Route::get('/knowledge/category/{category}', function (string $category) {
    return view('pages.knowledge.index', ['category' => $category]);
})->name('knowledge.category');

Route::get('/knowledge/{slug}', function (string $slug) {
    return view('pages.knowledge.show', ['slug' => $slug]);
})->name('knowledge.show');

Route::get('/about', function () {
    return view('pages.about.index');
})->name('about');

Route::get('/quote', function () {
    return view('pages.quote.index');
})->name('quote.index');

Route::get('/quote/success/{reference}', function (string $reference) {
    return view('pages.quote.success', ['reference' => $reference]);
})->name('quote.success');

Route::get('/quote/track/{reference}', function (string $reference) {
    return view('pages.quote.track', ['reference' => $reference]);
})->name('quote.track');

Route::get('/contact', function () {
    return view('pages.contact.index');
})->name('contact');

Route::get('/styleguide', function () {
    return view('styleguide');
})->name('styleguide');
