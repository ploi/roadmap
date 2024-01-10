<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        \App\Models\Project::query()->update(['icon' => null]);
    }
};
