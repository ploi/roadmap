<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Turns `@Full Name` and `@username` mentions in markdown into profile links.
 *
 * Full names may contain spaces, so every `@` is matched against the longest
 * run of up to MAX_WORDS words that equals a user's name. Code spans, fenced
 * code blocks and existing links are left untouched, which also keeps
 * already converted mentions from being parsed twice when a comment is edited.
 */
class MentionParser
{
    private const MAX_WORDS = 4;

    private const TRAILING_PUNCTUATION = '.,!?;:)\'"';

    private const SKIPPED_SEGMENTS = '/(```.*?```|`[^`\n]*`|\[[^\]]*\]\([^)]*\))/s';

    private const MENTION_START = '/(?<![\p{L}\p{N}_@\/.])@(?=[\p{L}\p{N}_])/u';

    /**
     * @return array{content: string, users: Collection<int, User>}
     */
    public function parse(string $content): array
    {
        $segments = preg_split(self::SKIPPED_SEGMENTS, $content, -1, PREG_SPLIT_DELIM_CAPTURE);

        $mentionsPerSegment = [];

        foreach ($segments as $index => $segment) {
            if ($index % 2 === 1) {
                continue;
            }

            preg_match_all(self::MENTION_START, $segment, $matches, PREG_OFFSET_CAPTURE);

            foreach ($matches[0] as [, $offset]) {
                $mentionsPerSegment[$index][] = [
                    'offset' => $offset,
                    'candidates' => $this->candidatesAfter($segment, $offset + 1),
                ];
            }
        }

        if ($mentionsPerSegment === []) {
            return ['content' => $content, 'users' => collect()];
        }

        [$usersByUsername, $usersByName] = $this->findUsers(
            collect($mentionsPerSegment)
                ->flatten(1)
                ->pluck('candidates')
                ->flatten(1)
                ->map(fn (array $candidate) => $this->normalize($candidate['text']))
                ->unique()
                ->values()
        );

        $mentionedUsers = collect();

        foreach ($mentionsPerSegment as $index => $mentions) {
            $segments[$index] = $this->replaceMentions(
                $segments[$index],
                $mentions,
                $usersByUsername,
                $usersByName,
                $mentionedUsers,
            );
        }

        return [
            'content' => implode('', $segments),
            'users' => $mentionedUsers->unique('id')->values(),
        ];
    }

    /**
     * The possible mention texts following an `@`, longest first.
     *
     * @return list<array{text: string, length: int}>
     */
    private function candidatesAfter(string $segment, int $start): array
    {
        $line = substr($segment, $start, strcspn($segment, "\r\n", $start));

        preg_match_all('/\S+/u', $line, $words, PREG_OFFSET_CAPTURE);

        $candidates = [];

        foreach (array_slice($words[0], 0, self::MAX_WORDS) as $position => [$word, $wordOffset]) {
            if ($position > 0 && str_starts_with($word, '@')) {
                break;
            }

            $text = rtrim(substr($line, 0, $wordOffset + strlen($word)), self::TRAILING_PUNCTUATION);

            $candidates[] = ['text' => $text, 'length' => strlen($text)];
        }

        return array_reverse($candidates);
    }

    /**
     * @param  Collection<int, string>  $normalizedCandidates
     * @return array{0: Collection<string, User>, 1: Collection<string, Collection<int, User>>}
     */
    private function findUsers(Collection $normalizedCandidates): array
    {
        $users = User::query()
            ->whereIn(DB::raw('LOWER(username)'), $normalizedCandidates)
            ->orWhereIn(DB::raw('LOWER(name)'), $normalizedCandidates)
            ->get();

        return [
            $users->keyBy(fn (User $user) => $this->normalize($user->username)),
            $users->groupBy(fn (User $user) => $this->normalize($user->name)),
        ];
    }

    /**
     * @param  list<array{offset: int, candidates: list<array{text: string, length: int}>}>  $mentions
     * @param  Collection<string, User>  $usersByUsername
     * @param  Collection<string, Collection<int, User>>  $usersByName
     * @param  Collection<int, User>  $mentionedUsers
     */
    private function replaceMentions(
        string $segment,
        array $mentions,
        Collection $usersByUsername,
        Collection $usersByName,
        Collection $mentionedUsers,
    ): string {
        $output = '';
        $cursor = 0;

        foreach ($mentions as $mention) {
            if ($mention['offset'] < $cursor) {
                continue;
            }

            foreach ($mention['candidates'] as $candidate) {
                $user = $this->resolveUser($candidate['text'], $usersByUsername, $usersByName);

                if (! $user) {
                    continue;
                }

                $output .= substr($segment, $cursor, $mention['offset'] - $cursor) . $this->link($user);
                $cursor = $mention['offset'] + 1 + $candidate['length'];
                $mentionedUsers->push($user);

                break;
            }
        }

        return $output . substr($segment, $cursor);
    }

    /**
     * A username always identifies one user. A name only counts when it is not shared by several users.
     *
     * @param  Collection<string, User>  $usersByUsername
     * @param  Collection<string, Collection<int, User>>  $usersByName
     */
    private function resolveUser(string $text, Collection $usersByUsername, Collection $usersByName): ?User
    {
        $normalized = $this->normalize($text);

        if ($usersByUsername->has($normalized)) {
            return $usersByUsername->get($normalized);
        }

        $usersWithName = $usersByName->get($normalized);

        return $usersWithName?->count() === 1 ? $usersWithName->first() : null;
    }

    private function link(User $user): string
    {
        $name = addcslashes($user->name, '\\`*_[]<>');

        return "[@{$name}](" . route('public-user', $user->username, false) . ')';
    }

    private function normalize(string $text): string
    {
        return mb_strtolower(preg_replace('/\s+/u', ' ', trim($text)));
    }
}
