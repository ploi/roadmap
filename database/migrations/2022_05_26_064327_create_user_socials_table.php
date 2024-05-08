<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_socials', function (Blueprint $table) {
            $table->id();

            $table->string('name')->nullable();
            $table->string('provider')->nullable()->default('sso');
            $table->string('provider_id')->nullable();
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();

            $table->foreignId('user_id')->nullable()->constrained();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_socials');
    }
};
