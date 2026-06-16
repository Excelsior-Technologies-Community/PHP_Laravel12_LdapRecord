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
        Schema::create('ldap_audit_logs', function (Blueprint $table) {
    $table->id();
    $table->string('username');
    $table->string('ip_address');
    $table->string('status'); // 'success' or 'failed'
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ldap_audit_logs');
    }
};
