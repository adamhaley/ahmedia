<?php

use App\Enums\PageTemplate;
use App\Models\Page;

return [

    /*
    | ahmedia's Page model extends Prism CMS core's to add its own columns and relations.
    */
    'models' => [
        'page' => Page::class,
    ],

    /*
    | ahmedia's templates. Each says which view renders it and what extra data it needs.
    */
    'page_template' => PageTemplate::class,

    /*
    | Prism CMS core registers the home route and the catch-all page route.
    */
    'routes' => true,

];
