<?php

namespace App\Services\Faq;

class FaqTypoCorrector
{
    /**
     * @param  list<string>  $tokens
     * @param  list<string>  $vocabulary
     * @return list<string>
     */
    public function correct(array $tokens, array $vocabulary): array
    {
        $known = array_fill_keys($vocabulary, true);

        return array_map(function (string $token) use ($known, $vocabulary): string {
            $length = mb_strlen($token);

            if ($length < 4 || isset($known[$token]) || ! ctype_alpha($token)) {
                return $token;
            }

            $limit = $length < 6 ? 1 : 2;
            $bestDistance = $limit + 1;
            $best = $token;
            $tied = false;

            foreach ($vocabulary as $candidate) {
                if (abs(strlen($candidate) - $length) > $limit) {
                    continue;
                }

                $distance = levenshtein($token, $candidate);

                if ($distance < $bestDistance) {
                    $bestDistance = $distance;
                    $best = $candidate;
                    $tied = false;
                } elseif ($distance === $bestDistance) {
                    $tied = true;
                }
            }

            return $bestDistance <= $limit && ! $tied ? $best : $token;
        }, $tokens);
    }
}
