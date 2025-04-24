<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name', 255);
            $table->string('username', 255)->unique();
            $table->string('password', 255);
            $table->string('bio', 255)->nullable();
            $table->tinyInteger('is_private')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('users');
    }
};


