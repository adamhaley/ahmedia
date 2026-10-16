<?php

declare(strict_types=1);

namespace App\Services\Faq;

use App\Enums\FaqMatchOutcome;
use App\Models\FaqItem;

class FaqMatchingService
{
    /**
     * Curated keywords outrank everything because they are the editorial
     * override: when the chatbot misses a phrasing, someone fixes it in the
     * CMS by adding a keyword, and that fix has to actually win. The question
     * is short and high-signal; the answer is long and noisy but carries
     * vocabulary the question never mentions ("weddings" lives only in answer
     * bodies), so it earns a small weight rather than none.
     */
    private const KeywordWeight = 6;

    private const QuestionWeight = 3;

    private const AnswerWeight = 1;

    /**
     * Tuned against FaqCorpusMatchingTest, which asserts real visitor
     * phrasings land on the right FAQ. Change these and run that test -- it
     * exists to make this empirical rather than a guess.
     */
    private const ConfidentScore = 0.45;

    /** How far ahead of the runner-up the top hit must be to answer outright. */
    private const ConfidentLead = 1.35;

    /**
     * Floor for a match with no competition at all. Deliberately lower than
     * ConfidentScore: cosine against a long answer body is capped by query
     * length, so a short question like "do you do weddings" tops out around
     * 0.14 even when it is unambiguously right. The lead ratio normally
     * carries that judgement, but with a single scoring document there is no
     * runner-up to compare against, leaving the absolute score to punish
     * brevity rather than irrelevance.
     */
    private const SoleMatchScore = 0.12;

    private const AmbiguousScore = 0.18;

    private const MaxOptions = 3;

    /** @var array{docs: array<int, array<string, float>>, idf: array<string, float>, items: array<int, FaqItem>}|null */
    private ?array $index = null;

    public function __construct(private FaqTokenizer $tokenizer, private FaqTypoCorrector $typoCorrector) {}

    public function match(string $question): FaqMatchResult
    {
        $index = $this->index();

        if ($index['items'] === []) {
            return FaqMatchResult::noMatch();
        }

        $queryTokens = $this->typoCorrector->correct(
            $this->tokenizer->tokenize($question),
            array_keys($index['idf']),
        );

        if ($queryTokens === []) {
            return FaqMatchResult::noMatch();
        }

        $queryVector = $this->vector(array_count_values($queryTokens), $index['idf']);

        if ($queryVector === []) {
            return FaqMatchResult::noMatch();
        }

        $scores = [];

        foreach ($index['docs'] as $id => $documentVector) {
            $score = $this->cosine($queryVector, $documentVector);

            if ($score > 0.0) {
                $scores[$id] = $score;
            }
        }

        arsort($scores);

        return $this->interpret($scores, $index['items']);
    }

    /**
     * @param  array<int, float>  $scores
     * @param  array<int, FaqItem>  $items
     */
    private function interpret(array $scores, array $items): FaqMatchResult
    {
        if ($scores === []) {
            return FaqMatchResult::noMatch();
        }

        $top = reset($scores);
        $runnerUp = count($scores) > 1 ? array_values($scores)[1] : 0.0;
        $id = array_key_first($scores);

        if ($runnerUp === 0.0) {
            return $top >= self::SoleMatchScore
                ? new FaqMatchResult(FaqMatchOutcome::Confident, [new FaqMatch($items[$id], $top)])
                : FaqMatchResult::noMatch();
        }

        if ($top >= self::ConfidentScore && $top >= self::ConfidentLead * $runnerUp) {
            return new FaqMatchResult(FaqMatchOutcome::Confident, [new FaqMatch($items[$id], $top)]);
        }

        if ($top < self::AmbiguousScore) {
            return FaqMatchResult::noMatch();
        }

        $options = [];

        foreach ($scores as $id => $score) {
            if ($score < self::AmbiguousScore || count($options) >= self::MaxOptions) {
                break;
            }

            $options[] = new FaqMatch($items[$id], $score);
        }

        return new FaqMatchResult(
            count($options) === 1 ? FaqMatchOutcome::Confident : FaqMatchOutcome::Ambiguous,
            $options,
        );
    }

    /**
     * Rebuilt per request rather than cached: 19 documents of a few hundred
     * words tokenize in well under a millisecond, and a cache would buy
     * nothing while adding an invalidation hook to every FAQ save. Revisit if
     * the corpus ever reaches the hundreds.
     *
     * @return array{docs: array<int, array<string, float>>, idf: array<string, float>, items: array<int, FaqItem>}
     */
    private function index(): array
    {
        if ($this->index !== null) {
            return $this->index;
        }

        $items = FaqItem::query()->orderBy('sort_order')->get()->keyBy('id')->all();

        $termCounts = [];
        $documentFrequency = [];

        foreach ($items as $id => $item) {
            $counts = [];

            $fields = [
                [$item->keywords, self::KeywordWeight],
                [$item->question, self::QuestionWeight],
                [$item->answer, self::AnswerWeight],
            ];

            foreach ($fields as [$text, $weight]) {
                foreach ($this->tokenizer->tokenize((string) $text) as $token) {
                    $counts[$token] = ($counts[$token] ?? 0) + $weight;
                }
            }

            $termCounts[$id] = $counts;

            foreach (array_keys($counts) as $term) {
                $documentFrequency[$term] = ($documentFrequency[$term] ?? 0) + 1;
            }
        }

        $total = count($items);
        $idf = [];

        // Smoothed so a term appearing in every FAQ still carries a little
        // weight. Plain log(N/df) would zero it out, and a query built only
        // from such words ("what services do you offer") would end up
        // with an empty vector and match nothing at all.
        foreach ($documentFrequency as $term => $frequency) {
            $idf[$term] = log(1 + $total / $frequency);
        }

        $docs = [];

        foreach ($termCounts as $id => $counts) {
            $docs[$id] = $this->vector($counts, $idf);
        }

        return $this->index = ['docs' => $docs, 'idf' => $idf, 'items' => $items];
    }

    /**
     * TF-IDF weights, L2-normalized so cosine reduces to a dot product and
     * long answers don't beat short questions on length alone.
     *
     * @param  array<string, int>  $counts
     * @param  array<string, float>  $idf
     * @return array<string, float>
     */
    private function vector(array $counts, array $idf): array
    {
        $vector = [];

        foreach ($counts as $term => $count) {
            if (isset($idf[$term])) {
                $vector[$term] = $count * $idf[$term];
            }
        }

        $norm = sqrt(array_sum(array_map(fn (float $value): float => $value ** 2, $vector)));

        if ($norm <= 0.0) {
            return [];
        }

        return array_map(fn (float $value): float => $value / $norm, $vector);
    }

    /**
     * @param  array<string, float>  $query
     * @param  array<string, float>  $document
     */
    private function cosine(array $query, array $document): float
    {
        $dot = 0.0;

        foreach ($query as $term => $value) {
            if (isset($document[$term])) {
                $dot += $value * $document[$term];
            }
        }

        return $dot;
    }
}
