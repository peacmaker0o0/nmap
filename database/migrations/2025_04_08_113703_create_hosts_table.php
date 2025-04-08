<?php

use App\Models\Host;
use App\Models\Range;
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
        Schema::create('hosts', function (Blueprint $table) {
            $table->id();
            $table->string('ip');
            $table->string('domain');
            $table->foreignIdFor(Range::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('host_status', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Host::class)->constrained()->cascadeOnDelete();
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hosts');
        Schema::dropIfExists('host_status');
    }
};
