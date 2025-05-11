<?php

use App\Models\Host;
use App\Models\ScanHistory;
use App\Models\VulnScanHistory;
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
        Schema::create('vuln_scan_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Host::class);
            $table->timestamps();
        });



        Schema::table('vulnerabilities', function (Blueprint $table) {
            $table->foreignIdFor(VulnScanHistory::class);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vuln_scan_histories');


        Schema::table('vulnerabilities', function (Blueprint $table) {
            $table->dropForeignIdFor(VulnScanHistory::class);
        });
    }
};
