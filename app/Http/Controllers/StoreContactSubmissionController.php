<?php

namespace App\Http\Controllers;

use App\Enums\ContactSubmissionSource;
use App\Http\Requests\StoreContactSubmissionRequest;
use App\Services\ContactSubmissions\ContactSubmissionIntakeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class StoreContactSubmissionController extends Controller
{
    public function __invoke(StoreContactSubmissionRequest $request, ContactSubmissionIntakeService $intake): JsonResponse|RedirectResponse
    {
        $intake->record(
            source: ContactSubmissionSource::ContactForm,
            name: $request->string('name')->trim()->toString(),
            email: $request->string('email')->trim()->toString(),
            message: $request->string('message')->trim()->toString(),
            submittedFrom: $request->headers->get('referer'),
            ipAddress: $request->ip(),
        );

        $message = "Thanks for reaching out! We'll get back to you within one business day.";

        if ($request->wantsJson()) {
            return response()->json(['message' => $message], 201);
        }

        return redirect()->to(url()->previous().'#contact')->with('contact_status', $message);
    }
}
