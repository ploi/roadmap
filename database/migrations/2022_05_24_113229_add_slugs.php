<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('slug')->after('id')->index()->nullable();
        });

        Schema::table('boards', function (Blueprint $table) {
            $table->string('slug')->after('id')->index()->nullable();
        });

        Schema::table('items', function (Blueprint $table) {
            $table->string('slug')->after('id')->index()->nullable();
        });

        \App\Models\Project::each(fn(\App\Models\Project $project) => $project->update(['slug' => Str::slug($project->id . ' ' . $project->title)]));
        \App\Models\Board::each(fn(\App\Models\Board $board) => $board->update(['slug' => Str::slug($board->id . ' ' . $board->title)]));
        \App\Models\Item::each(fn(\App\Models\Item $item) => $item->update(['slug' => Str::slug($item->id . ' ' . $item->title)]));
    }
};
