<?php

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
        Schema::table('events', function (Blueprint $table) {
            $table->string('foto_event')->nullable()->after('nama_event');
            $table->time('jam_event')->nullable()->after('tanggal_event');
            $table->string('nama_panitia')->nullable()->after('jam_event');
            $table->decimal('harga_pendaftaran', 15, 2)->default(0)->after('nama_panitia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['foto_event', 'jam_event', 'nama_panitia', 'harga_pendaftaran']);
        });
    }
};
