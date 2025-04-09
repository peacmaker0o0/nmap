<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Models\Range;

Route::middleware(['auth'])->group(function () {
    Volt::route('ranges/create', 'ranges.create')->name('ranges.create');
    Volt::route('ranges/edit/{range}', 'ranges.edit')->name('ranges.edit');
    
    // Other routes
    Volt::route('ranges/{range}', 'ranges.show')->name('ranges.show');
    Volt::route('ranges', 'ranges.all')->name('ranges.all');  // Removed trailing slash
});