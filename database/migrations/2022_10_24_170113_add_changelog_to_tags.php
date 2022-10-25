<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up()
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->boolean('changelog')->after('type')->default(false);
        });
    }
};
