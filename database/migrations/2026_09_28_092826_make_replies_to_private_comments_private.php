<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Replies to private notes used to be saved as public comments. Mark them (and their nested replies) private.
     */
    public function up(): void
    {
        do {
            $replyIds = DB::table('comments as replies')
                ->join('comments as parents', 'parents.id', '=', 'replies.parent_id')
                ->where('parents.private', true)
                ->where('replies.private', false)
                ->pluck('replies.id');

            DB::table('comments')->whereIn('id', $replyIds)->update(['private' => true]);
        } while ($replyIds->isNotEmpty());
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
