<?php

namespace App\Http\Controllers\Chat;

use App\Enums\ContactSubmissionSource;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChatLeadRequest;
use App\Models\ChatQuery;
use App\Services\ContactSubmissions\ContactSubmissionIntakeService;
use Illuminate\Http\JsonResponse;

class StoreChatLeadController extends Controller
{
    public function __invoke(StoreChatLeadRequest $request, ContactSubmissionIntakeService $intake): JsonResponse
    {
        $question = $request->string('question')->trim()->toString();

        $submission = $intake->record(
            source: ContactSubmissionSource::Chat,
            name: $request->string('name')->trim()->toString(),
            email: $request->string('email')->trim()->toString(),
            message: $question,
            submittedFrom: $request->headers->get('referer'),
            ipAddress: $request->ip(),
        );

        // Best-effort link back to the ChatQuery the question was logged
        // under, so the chat log shows which unanswered questions became leads.
        ChatQuery::query()
            ->where('question', $question)
            ->whereNull('contact_submission_id')
            ->latest('id')
            ->first()
            ?->update(['contact_submission_id' => $submission->id]);

        return response()->json([
            'message' => "Thanks! We've passed your question along and will follow up by email shortly.",
        ], 201);
    }
}
