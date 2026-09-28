<?php

use App\Services\MentionParser;

function parseMentions(string $content): array
{
    return app(MentionParser::class)->parse($content);
}

test('a full name mention becomes a profile link', function () {
    $kenneth = createUser(['name' => 'Kenneth Wolfs']);

    $result = parseMentions('Hey @Kenneth Wolfs does this work?');

    expect($result['content'])->toBe('Hey [@Kenneth Wolfs](/user/kenneth-wolfs) does this work?')
        ->and($result['users']->pluck('id')->all())->toBe([$kenneth->id]);
});

test('a full name mention is matched case insensitively', function () {
    createUser(['name' => 'Kenneth Wolfs']);

    expect(parseMentions('@kenneth wolfs')['content'])->toBe('[@Kenneth Wolfs](/user/kenneth-wolfs)');
});

test('a username mention becomes a profile link with the full name', function () {
    createUser(['name' => 'Alex Bouma']);

    expect(parseMentions('Thanks @alex-bouma!')['content'])->toBe('Thanks [@Alex Bouma](/user/alex-bouma)!');
});

test('the longest matching name wins over a shorter username', function () {
    createUser(['name' => 'Alex']);
    createUser(['name' => 'Alex Bouma']);

    expect(parseMentions('@Alex Bouma, hi')['content'])->toBe('[@Alex Bouma](/user/alex-bouma), hi');
});

test('several mentions in one line are all linked', function () {
    createUser(['name' => 'Alex Bouma']);
    createUser(['name' => 'Kenneth Wolfs']);

    expect(parseMentions('@Alex Bouma @Kenneth Wolfs')['content'])
        ->toBe('[@Alex Bouma](/user/alex-bouma) [@Kenneth Wolfs](/user/kenneth-wolfs)');
});

test('a name shared by several users is not linked', function () {
    createUser(['name' => 'Jane Doe']);
    createUser(['name' => 'Jane Doe']);

    $result = parseMentions('@Jane Doe');

    expect($result['content'])->toBe('@Jane Doe')
        ->and($result['users'])->toBeEmpty();
});

test('mentions in code and existing links are left untouched', function () {
    createUser(['name' => 'Alex Bouma']);

    $content = "`@alex-bouma` [@Alex Bouma](/user/alex-bouma)\n```\n@alex-bouma\n```";

    $result = parseMentions($content);

    expect($result['content'])->toBe($content)
        ->and($result['users'])->toBeEmpty();
});

test('email addresses are not mentions', function () {
    createUser(['name' => 'Example']);

    expect(parseMentions('mail me at me@example.com')['content'])->toBe('mail me at me@example.com');
});

test('unknown mentions are left as typed', function () {
    expect(parseMentions('@Nobody Here')['content'])->toBe('@Nobody Here');
});

test('markdown characters in a name are escaped in the link', function () {
    createUser(['name' => 'Alex_Bouma*']);

    expect(parseMentions('@Alex_Bouma*')['content'])->toBe('[@Alex\_Bouma\*](/user/alex-bouma)');
});
