<?php

use App\Enums\ButtonStyle;
use App\Enums\ContactSubmissionClassification;
use App\Enums\ContactSubmissionSource;
use App\Enums\FaqMatchOutcome;
use App\Enums\HeroRingsMotion;
use App\Enums\PageTemplate;
use App\Enums\ProjectType;

return [
    ButtonStyle::class => [
        'primary' => 'Primary',
        'secondary' => 'Secondary',
    ],
    ContactSubmissionClassification::class => [
        'valid' => 'Valid',
        'spam' => 'Spam',
        'unanswerable' => 'Unanswerable',
        'needs_human' => 'Needs human',
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
    HeroRingsMotion::class => [
        'auto' => 'Auto (timed loop)',
        'scroll' => 'Scroll (pinned hero)',
        'still' => 'Still (one held frame)',
    ],
    PageTemplate::class => [
        'about' => 'About',
        'blog' => 'Blog index',
        'case_studies_index' => 'Case studies index',
        'contact' => 'Contact',
        'faq' => 'FAQ',
        'home' => 'Home',
        'logo' => 'Logo story',
        'service' => 'Service',
        'services_index' => 'Services index',
        'standard' => 'Standard',
    ],
    ProjectType::class => [
        'website' => 'Websites',
        'workflow' => 'Workflows',
    ],
];
