<?php

use App\Models\Host;
use App\Models\ScanHistory;
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
        Schema::create('scan_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Host::class);
            $table->timestamps();
        });


        Schema::table('services', function (Blueprint $table) {
            $table->foreignIdFor(ScanHistory::class);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scan_history');
    }
};
