<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactSubmissionRequest;
use App\Models\ContactSubmission;
use App\Notifications\NewContactSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Notification;
use Throwable;

class StoreContactSubmissionController extends Controller
{
    public function __invoke(StoreContactSubmissionRequest $request): JsonResponse|RedirectResponse
    {
        $submission = ContactSubmission::query()->create([
            'name' => $request->string('name')->trim()->toString(),
            'email' => $request->string('email')->trim()->toString(),
            'message' => $request->string('message')->trim()->toString(),
            'submitted_from' => $request->headers->get('referer'),
            'ip_address' => $request->ip(),
        ]);

        // Sent inline rather than queued: one email per submission doesn't
        // need a worker. The lead is already saved, so a mail failure is
        // reported but never shown to the visitor or allowed to lose it.
        try {
            Notification::route('mail', config('mail.lead_notification_address'))
                ->notify(new NewContactSubmission($submission));
        } catch (Throwable $exception) {
            report($exception);
        }

        $message = "Thanks for reaching out! We'll get back to you within one business day.";

        if ($request->wantsJson()) {
            return response()->json(['message' => $message], 201);
        }

        return redirect()->to(url()->previous().'#contact')->with('contact_status', $message);
    }
}
