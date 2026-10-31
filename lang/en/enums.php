<?php

use App\Enums\ButtonStyle;
use App\Enums\ContactSubmissionSource;
use App\Enums\FaqMatchOutcome;
use App\Enums\PageTemplate;

return [
    ButtonStyle::class => [
        'primary' => 'Primary',
        'secondary' => 'Secondary',
    ],
    ContactSubmissionSource::class => [
        'contact_form' => 'Contact form',
        'chat' => 'Chat',
    ],
    FaqMatchOutcome::class => [
        'confident' => 'Answered',
        'ambiguous' => 'Offered options',
        'no_match' => 'No match',
    ],
    PageTemplate::class => [
        'case_studies_index' => 'Case studies index',
        'contact' => 'Contact',
        'faq' => 'FAQ',
        'home' => 'Home',
        'logo' => 'Logo story',
        'service' => 'Service',
        'services_index' => 'Services index',
        'standard' => 'Standard',
    ],
];
