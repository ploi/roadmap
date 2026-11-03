<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_endpoints', function (Blueprint $table) {
            $table->id();
            $table->string('url', 2048);
            $table->text('secret');
            $table->json('events');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_endpoints');
    }
};
