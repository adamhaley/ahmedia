<?php

namespace Tests\Services\Faq;

use App\Services\Faq\FaqTypoCorrector;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FaqTypoCorrectorTest extends TestCase
{
    #[Test]
    #[DataProvider('tokens')]
    public function it_corrects_only_unique_nearby_vocabulary_terms(array $tokens, array $vocabulary, array $expected): void
    {
        $actual = (new FaqTypoCorrector)->correct($tokens, $vocabulary);

        $this->assertSame($expected, $actual);
    }

    public static function tokens(): array
    {
        return [
            'known terms' => [['cost'], ['cost', 'cast'], ['cost']],
            'four letter substitution' => [['cost'], ['cast'], ['cast']],
            'non alphabetic unchanged' => [['pr ice'], ['price'], ['pr ice']],
            'five letter two edits rejected' => [['prcie'], ['price'], ['prcie']],
            'one edit' => [['pric'], ['price'], ['price']],
            'long transposition' => [['aerail'], ['aerial'], ['aerial']],
            'long deletion' => [['insurnce'], ['insurance'], ['insurance']],
            'short words untouched' => [['cat'], ['cost', 'car'], ['cat']],
            'equidistant tie' => [['cotz'], ['cost', 'cats'], ['cotz']],
            'tie within threshold' => [['cast'], ['cost', 'cart'], ['cast']],
            'too distant' => [['cookie'], ['aerial'], ['cookie']],
            'no vocabulary' => [['aerail'], [], ['aerail']],
            'no tokens' => [[], ['aerial'], []],
            'repeated tokens' => [['aerail', 'aerail'], ['aerial'], ['aerial', 'aerial']],
            'unicode unchanged' => [['café'], ['cafe'], ['café']],
        ];
    }
}
