<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up()
    {
        Schema::table('boards', function (Blueprint $table) {
            $table->integer('sort_order')->nullable()->after('project_id');
        });
    }

    public function down()
    {
        Schema::table('boards', function (Blueprint $table) {
            $table->integer('sort_order')->nullable();
        });
    }
};
