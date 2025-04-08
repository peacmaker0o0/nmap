<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware(['auth'])->group(function () {
    Volt::route('ranges/create', 'ranges.create')->name('ranges.create');
    Volt::route('ranges/all', 'ranges.all')->name('ranges.all');
    Volt::route('ranges/{range}', 'ranges.show')->name('ranges.show');


});