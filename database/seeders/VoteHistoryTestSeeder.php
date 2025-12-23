<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;

class VoteHistoryTestSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::factory()->count(20)->create();

        $item = Item::factory()->create([
            'title' => 'Test Item for Vote History',
            'user_id' => $users->first()->id,
        ]);

        $voteDistribution = [
            0 => 2,
            1 => 3,
            2 => 1,
            3 => 5,
            4 => 2,
            5 => 0,
            6 => 4,
            7 => 3,
            8 => 6,
            9 => 2,
            10 => 1,
            11 => 4,
            12 => 3,
            13 => 5,
        ];

        $userIndex = 0;
        foreach ($voteDistribution as $daysAgo => $voteCount) {
            $date = Carbon::now()->subDays($daysAgo);

            for ($i = 0; $i < $voteCount; $i++) {
                if ($userIndex >= $users->count()) {
                    break 2;
                }

                $vote = new Vote();
                $vote->user_id = $users[$userIndex]->id;
                $vote->model_type = Item::class;
                $vote->model_id = $item->id;
                $vote->subscribed = fake()->boolean(30);
                $vote->created_at = $date;
                $vote->updated_at = $date;
                $vote->save();

                $userIndex++;
            }
        }

        $item->total_votes = $item->votes()->count();
        $item->save();

        $this->command->info("Created item '{$item->title}' with {$item->total_votes} votes over 14 days.");
        $this->command->info("View at: /items/{$item->slug}");
    }
}
