<?php

declare(strict_types=1);

namespace App\Services\Faq;

class FaqTokenizer
{
    /**
     * Contentless function words only. Domain-common words (site, app,
     * service) are deliberately NOT listed -- IDF already drives those
     * toward zero on its own, and hard-coding them here would mean editing
     * PHP every time the FAQ content shifts.
     *
     * Words shorter than MinTokenLength never reach this list, so there's no
     * need to enumerate "is", "of", "to" and friends.
     *
     * @var list<string>
     */
    private const StopWords = [
        'the', 'and', 'are', 'for', 'with', 'that', 'this', 'from', 'they', 'them', 'their',
        'there', 'these', 'those', 'have', 'has', 'had', 'was', 'were', 'been', 'being',
        'will', 'would', 'could', 'should', 'can', 'does', 'did', 'you', 'your', 'yours',
        'our', 'ours', 'its', 'but', 'not', 'any', 'all', 'into', 'over', 'under', 'then',
        'than', 'when', 'where', 'which', 'who', 'whom', 'why', 'how', 'what', 'about',
        'just', 'also', 'like', 'one', 'out', 'get', 'got', 'use', 'per', 'via', 'too',
        'very', 'more', 'most', 'some', 'such', 'only', 'own', 'same', 'each', 'few',
        'other', 'others', 'said', 'say', 'says', 'were', 'want', 'need', 'looking', 'ones',
    ];

    private const MinTokenLength = 3;

    /**
     * Derivational suffixes, longest first so "ance" wins before "er" or "ed"
     * can nibble at the end of the same word. Plurals are handled separately
     * and always first -- see stem().
     *
     * @var list<string>
     */
    private const Suffixes = ['ance', 'ment', 'ness', 'tion', 'ing', 'ers', 'er', 'ed', 'ly'];

    /**
     * @return list<string>
     */
    public function tokenize(string $text): array
    {
        $words = preg_split('/[^a-z0-9]+/', mb_strtolower($this->stripHtml($text)), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        $tokens = [];

        foreach ($words as $word) {
            if (mb_strlen($word) < self::MinTokenLength || in_array($word, self::StopWords, true)) {
                continue;
            }

            $tokens[] = $this->stem($word);
        }

        return $tokens;
    }

    /**
     * Every tag becomes a space rather than being deleted, because FAQ answers
     * are RichEditor output where paragraphs butt straight up against each
     * other -- a bare strip_tags() turns "...rigging.</p><p>Installation..."
     * into the single nonsense token "riggingInstallation".
     */
    public function stripHtml(string $html): string
    {
        return html_entity_decode(
            preg_replace('/<[^>]*>/', ' ', $html) ?? $html,
            ENT_QUOTES | ENT_HTML5,
        );
    }

    /**
     * Deliberately lossy: it only has to be *consistent* across a 19-document
     * corpus, not linguistically correct. The trailing-"e" trim at the end is
     * what makes the pairs that matter here collide -- "price" and "pricing"
     * both land on "pric".
     */
    public function stem(string $word): string
    {
        $word = $this->singularize($word);
        $word = $this->stripSuffix($word);

        if (str_ends_with($word, 'e') && mb_strlen($word) > self::MinTokenLength) {
            $word = mb_substr($word, 0, -1);
        }

        return $word;
    }

    /**
     * Plurals come off before derivational suffixes, never mixed in with them.
     * A general "es" rule looks tempting but shortens "performances" to
     * "performanc", which can then never shed "ance" to meet "performance" at
     * "perform". Stripping the bare "s" first leaves "performance" intact for
     * the next step, which is exactly what Porter does too.
     */
    private function singularize(string $word): string
    {
        return match (true) {
            str_ends_with($word, 'sses') => mb_substr($word, 0, -2),
            str_ends_with($word, 'ies') && mb_strlen($word) > 4 => mb_substr($word, 0, -3).'y',
            str_ends_with($word, 'ss') => $word,
            str_ends_with($word, 's') && mb_strlen($word) > self::MinTokenLength => mb_substr($word, 0, -1),
            default => $word,
        };
    }

    private function stripSuffix(string $word): string
    {
        foreach (self::Suffixes as $suffix) {
            if (! str_ends_with($word, $suffix)) {
                continue;
            }

            $stem = mb_substr($word, 0, -mb_strlen($suffix));

            if (mb_strlen($stem) >= self::MinTokenLength) {
                return $stem;
            }
        }

        return $word;
    }
}
