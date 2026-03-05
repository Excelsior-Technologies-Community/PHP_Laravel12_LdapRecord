<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('ldap_users', function (Blueprint $table) {
            $table->id();
            $table->string('guid')->unique();
            $table->string('username');
            $table->string('email')->nullable();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('ldap_users');
    }
};