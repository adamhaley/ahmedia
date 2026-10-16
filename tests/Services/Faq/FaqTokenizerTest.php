<?php

namespace Tests\Services\Faq;

use App\Services\Faq\FaqTokenizer;
use Generator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(FaqTokenizer::class)]
class FaqTokenizerTest extends TestCase
{
    private FaqTokenizer $tokenizer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tokenizer = new FaqTokenizer;
    }

    #[Test]
    public function it_lowercases_and_splits_on_punctuation(): void
    {
        $this->assertSame(['aerial', 'silk'], $this->tokenizer->tokenize('Aerial, SILKS!'));
    }

    #[Test]
    public function it_drops_tokens_shorter_than_three_characters(): void
    {
        $this->assertSame(['ceil'], $this->tokenizer->tokenize('is it a ceiling'));
    }

    #[Test]
    public function it_removes_stopwords(): void
    {
        $this->assertSame([], $this->tokenizer->tokenize('what are the ones that you would have'));
    }

    #[Test]
    public function it_strips_html_tags_and_decodes_entities(): void
    {
        $this->assertSame(['bar', 'bat', 'mitzvah'], $this->tokenizer->tokenize('<p>Bar &amp; Bat <strong>Mitzvahs</strong></p>'));
    }

    /**
     * A bare strip_tags() would glue these into one nonsense token
     * ("rigginstalla"), which silently removes both real words from the
     * index. Two distinct tokens is the actual assertion here -- exact
     * stemming accuracy for "rigging" is covered separately.
     */
    #[Test]
    public function it_does_not_glue_words_across_adjacent_block_tags(): void
    {
        $tokens = $this->tokenizer->tokenize('<p>rigging.</p><p>Installation</p>');

        $this->assertCount(2, $tokens, 'gluing across the tag boundary would collapse this to a single token');
    }

    #[Test]
    #[DataProvider('equivalentWordProvider')]
    public function it_stems_related_words_to_a_shared_token(string $describe, array $words): void
    {
        $stems = array_unique(array_map(fn (string $word): string => $this->tokenizer->stem($word), $words));

        $this->assertCount(1, $stems, $describe.' should share one stem, got: '.implode(', ', $stems));
    }

    public static function equivalentWordProvider(): Generator
    {
        yield 'price family' => ['price/pricing/priced/prices', ['price', 'pricing', 'priced', 'prices']];
        yield 'cost family' => ['cost/costs/costing', ['cost', 'costs', 'costing']];
        yield 'wedding plural' => ['wedding/weddings', ['wedding', 'weddings']];
        yield 'perform family' => ['perform/performer/performance', ['perform', 'performer', 'performers', 'performance', 'performances']];
        yield 'insurance family' => ['insurance/insured', ['insurance', 'insured']];
        yield 'train family' => ['train/training/trained', ['train', 'training', 'trained']];
        yield 'double-s words' => ['address/addresses', ['address', 'addresses']];
    }

    /**
     * Guards the plural rules specifically: a general "es" strip would leave
     * "performanc", which can never meet "performance" at "perform".
     */
    #[Test]
    public function it_strips_the_plural_before_the_derivational_suffix(): void
    {
        $this->assertSame('perform', $this->tokenizer->stem('performances'));
    }

    #[Test]
    public function it_returns_no_tokens_for_an_empty_string(): void
    {
        $this->assertSame([], $this->tokenizer->tokenize(''));
    }

    /**
     * Documents a known, accepted gap rather than hiding it: this naive
     * stemmer does not undo English's doubled-consonant-before-"ing" rule
     * (rig -> rigging), which real Porter stemming handles via CVC pattern
     * detection. Left as-is because it only affects this one word pair in
     * the FAQ corpus, and database/seeders/data/faq-keywords.php lists both
     * "rig" and "rigging" as separate literal keywords specifically to route
     * around it. If this assertion ever starts failing, the workaround in
     * that file should be removed at the same time.
     */
    #[Test]
    public function it_does_not_collide_rig_and_rigging(): void
    {
        $this->assertNotSame($this->tokenizer->stem('rig'), $this->tokenizer->stem('rigging'));
    }
}
