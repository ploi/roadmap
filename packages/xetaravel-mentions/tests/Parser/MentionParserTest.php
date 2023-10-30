<?php
namespace Test\Parser;

use Tests\TestCase;
use Tests\vendor\Models\User;
use Tests\vendor\Models\Article;
use Tests\vendor\Parser\CustomParser;
use Xetaio\Mentions\Parser\MentionParser;

class MentionParserTest extends TestCase
{
    /**
     * @var \Xetaio\Mentions\Parser\MentionParser
     */
    protected $parser;

    /**
     * @var \Tests\ModelsTest\Article
     */
    protected $article;

    /**
     * Triggered before each test.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $user = User::find(1);
        $this->be($user);

        $this->article = Article::create([
            'user_id' => 1,
            'title' => 'test',
            'content' => ''
        ]);

        $this->parser = new MentionParser($this->article);
    }

    /**
     * testParse method
     *
     * @return void
     */
    public function testParse()
    {
        $input = 'Lorem ipsu @admin crepu @member.';
        $output = 'Lorem ipsu [@Admin](/users/profile/@Admin) crepu [@Member](/users/profile/@Member).';

        $result = $this->parser->parse($input);
        $this->assertSame($output, $result);

        $user = User::find(1);
        $this->assertSame(0, $user->notifications->count(), 'Should not mention the author of the mention.');

        $user = User::find(2);
        $this->assertSame(1, $user->notifications->count());

        $mentions = $this->article->mentions();
        $this->assertTrue($mentions->contains('username', 'Member'), 'Shoudl notify the user.');
        $this->assertFalse($mentions->contains('username', 'Admin'), 'Should not notify the author of the mention.');
    }

    /**
     * testParseWithoutInput method
     *
     * @return void
     */
    public function testParseWithoutInput()
    {
        $input = '';
        $output = '';

        $result = $this->parser->parse($input);
        $this->assertSame($output, $result);

        $input = null;
        $output = null;

        $result = $this->parser->parse($input);
        $this->assertSame($output, $result);
    }

    /**
     * testParseWithLargestMentions method
     *
     * @return void
     */
    public function testParseWithLargestMentions()
    {
        $input = 'Lorem ipsu @admin crepu @admin2 rhoncus id velit @adm sit amet @administrator.';
        $output = 'Lorem ipsu [@Admin](/users/profile/@Admin) crepu @admin2 rhoncus id velit @adm sit amet @administrator.';

        $result = $this->parser->parse($input);
        $this->assertSame($output, $result);
    }

    /**
     * testParseWithMentionsAtStart method
     *
     * @return void
     */
    public function testParseWithMentionsAtStart()
    {
        $input = '@Admin crepu rhoncus id velit @adm sit amet @administrator.';
        $output = '[@Admin](/users/profile/@Admin) crepu rhoncus id velit @adm sit amet @administrator.';

        $this->parser->setOption('regex', '/({character}{pattern}{rules})/');
        $result = $this->parser->parse($input);
        $this->assertSame($output, $result);
    }

    /**
     * testParseWithoutMention method
     *
     * @return void
     */
    public function testParseWithoutMention()
    {
        $input = 'Lorem ipsu @admin.';
        $output = 'Lorem ipsu [@Admin](/users/profile/@Admin).';

        $this->parser->setOption('mention', false);
        $result = $this->parser->parse($input);
        $this->assertSame($output, $result);
    }

    /**
     * testParseWithFakeMentionsAndWithoutMentions method
     *
     * @return void
     */
    public function testParseWithFakeMentionsAndWithoutMentions()
    {
        $input = 'Lorem ipsu @admin crepu @thisuserdoesntexist but this one @member yes. Also this blabla@admin is a non-normal mention.';
        $output = 'Lorem ipsu [@Admin](/users/profile/@Admin) crepu @thisuserdoesntexist but this one [@Member](/users/profile/@Member) yes. Also this blabla[@Admin](/users/profile/@Admin) is a non-normal mention.';

        $this->parser->setOption('mention', false);
        $result = $this->parser->parse($input);
        $this->assertSame($output, $result);
    }

    /**
     * testParseWithMentionWithoutNotifyWithoutDuplicate method
     *
     * @return void
     */
    public function testParseWithMentionWithoutNotifyWithoutDuplicate()
    {
        $input = 'Lorem ipsu @admin quis nostrud @nope exerci tation ullamcorper @member.';
        $output = 'Lorem ipsu [@Admin](/users/profile/@Admin) quis nostrud @nope exerci tation ullamcorper [@Member](/users/profile/@Member).';

        $this->parser
            ->setOption('mention', true)
            ->setOption('notify', false);

        $result = $this->parser->parse($input);
        $this->assertSame($output, $result);

        $mentions = $this->article->mentions();
        $this->assertSame(1, $mentions->count());
        $this->assertTrue($mentions->contains('username', 'Member'));
        $this->assertFalse($mentions->contains('username', 'Admin'));
        $this->assertFalse($mentions->contains('username', 'Nope'));

        $input = 'Lorem ipsu @member quis nostrud @nope exerci tation ullamcorper @admin.';
        $output = 'Lorem ipsu [@Member](/users/profile/@Member) quis nostrud @nope exerci tation ullamcorper [@Admin](/users/profile/@Admin).';

        $result = $this->parser->parse($input);
        $this->assertSame($output, $result);

        $mentions = $this->article->mentions();
        $this->assertSame(1, $mentions->count());
    }

    /**
     * testCustomParser method
     *
     * @return void
     */
    public function testCustomParser()
    {
        $input = 'Lorem ipsu @admin crepu @member.';
        $output = 'Lorem ipsu <a class="link" href="/users/show/@Admin">@Admin</a> crepu <a class="link" href="/users/show/@Member">@Member</a>.';

        $parser = new CustomParser($this->article);
        $result = $parser->parse($input);
        $this->assertSame($output, $result);
    }
}
