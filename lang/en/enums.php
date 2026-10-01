<?php

use App\Enums\ButtonStyle;
use App\Enums\PageTemplate;

return [
    ButtonStyle::class => [
        'primary' => 'Primary',
        'secondary' => 'Secondary',
    ],
    PageTemplate::class => [
        'contact' => 'Contact',
        'home' => 'Home',
        'service' => 'Service',
        'services_index' => 'Services index',
        'standard' => 'Standard',
    ],
];
