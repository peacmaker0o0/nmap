<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware(['auth'])->group(function () {
    Volt::route('ranges/create', 'ranges.create')->name('ranges.create');
    Volt::route('ranges/index', 'ranges.index')->name('ranges.index');


});