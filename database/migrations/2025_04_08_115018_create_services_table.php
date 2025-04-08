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
            $table->string('name');
            $table->string('port');
            $table->string('protocol');
            $table->timestamps();
        });

        Schema::create('host_service', callback: function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Host::class); //host_id
            $table->foreignIdFor(Service::class); //service_id
            $table->string('state');
            $table->timestamps();
            


        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
        Schema::dropIfExists('host_service');
    }
};
