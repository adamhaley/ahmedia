<?php

namespace App\Http\Controllers\Chat;

use App\Enums\ContactSubmissionSource;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChatLeadRequest;
use App\Models\ChatQuery;
use App\Models\ContactSubmission;
use App\Notifications\NewContactSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Notification;
use Throwable;

class StoreChatLeadController extends Controller
{
    public function __invoke(StoreChatLeadRequest $request): JsonResponse
    {
        $question = $request->string('question')->trim()->toString();

        $submission = ContactSubmission::query()->create([
            'name' => $request->string('name')->trim()->toString(),
            'email' => $request->string('email')->trim()->toString(),
            'message' => $question,
            'source' => ContactSubmissionSource::Chat,
            'submitted_from' => $request->headers->get('referer'),
            'ip_address' => $request->ip(),
        ]);

        // Best-effort link back to the ChatQuery the question was logged
        // under, so the chat log shows which unanswered questions became leads.
        ChatQuery::query()
            ->where('question', $question)
            ->whereNull('contact_submission_id')
            ->latest('id')
            ->first()
            ?->update(['contact_submission_id' => $submission->id]);

        // Same as the contact form: the lead is already saved, so a mail
        // failure is reported but never shown to the visitor.
        try {
            Notification::route('mail', config('mail.lead_notification_address'))
                ->notify(new NewContactSubmission($submission));
        } catch (Throwable $exception) {
            report($exception);
        }

        return response()->json([
            'message' => "Thanks! We've passed your question along and will follow up by email shortly.",
        ], 201);
    }
}
