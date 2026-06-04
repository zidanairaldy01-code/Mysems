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
        Schema::table('registrations', function (Blueprint $table) {
            $table->string('email')->nullable()->after('nomor_wa');
            $table->string('qr_code')->nullable()->unique()->after('email');
            $table->timestamp('attended_at')->nullable()->after('qr_code');
            $table->unsignedBigInteger('user_id')->nullable()->after('event_id'); // Link to user if logged in
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn(['email', 'qr_code', 'attended_at', 'user_id']);
        });
    }
};
