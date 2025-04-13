<?php

use App\Models\Host;
use App\Models\Service;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('services', callback: function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('port')->nullable();
            $table->string('protocol')->nullable();
            $table->string('version')->nullable();
            $table->foreignIdFor(Host::class);
            $table->enum('status', ['up', 'down']);  // Track service status
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
