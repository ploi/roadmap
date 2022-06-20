<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->boolean('private')->after('pinned')->default(false);
        });
    }
};
