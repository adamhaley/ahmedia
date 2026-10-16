<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Http\Requests\AskChatQuestionRequest;
use App\Models\ChatQuery;
use App\Services\Chat\ChatIntentRouter;
use App\Services\Faq\FaqMatchingService;
use Illuminate\Http\JsonResponse;

class AskChatQuestionController extends Controller
{
    public function __invoke(AskChatQuestionRequest $request, FaqMatchingService $matcher, ChatIntentRouter $router): JsonResponse
    {
        $question = $request->string('question')->trim()->toString();
        $reply = $router->route($question);

        if ($reply !== null) {
            return response()->json([
                'outcome' => $reply->intent->value,
                'reply' => $reply->reply,
                'matches' => [],
            ]);
        }

        $result = $matcher->match($question);
        $best = $result->best();

        ChatQuery::query()->create([
            'question' => $question,
            'outcome' => $result->outcome,
            'score' => $best?->score,
            'matched_faq_id' => $best?->faq->id,
        ]);

        return response()->json([
            'outcome' => $result->outcome->value,
            'matches' => array_map(fn ($match): array => [
                'id' => $match->faq->id,
                'question' => $match->faq->question,
                'answer' => $match->faq->answer,
            ], $result->matches),
        ]);
    }
}
