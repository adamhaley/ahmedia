<?php

/*
 * n8n workflow cards for the Automations page, in display order. Covers are
 * 1920px WebP renders of the workflow canvas, made by the n8n repo's
 * screenshots/capture.py; `images` are extra gallery slides as [file, alt text].
 */
return [
    [
        'slug' => 'telegram-field-report-capture',
        'title' => 'Telegram Field Report Capture',
        'description' => 'Send photos, voice notes and text to a Telegram bot while you work. When you are done, it transcribes the voice notes, has AI write a structured field report and files it with the photos to a folder or Google Drive. Sessions are kept in Postgres, so an interruption never loses what you captured.',
        'tags' => ['Telegram', 'OpenAI', 'Whisper', 'Postgres', 'Google Drive'],
        'cover_image' => 'telegram-field-report-capture.webp',
        'images' => [
            ['telegram-field-report-capture-full.webp', 'The full Telegram Field Report Capture workflow, with access control and Google Drive output'],
        ],
    ],
    [
        'slug' => 'back-up-n8n-workflows-to-github',
        'title' => 'Back Up n8n Workflows to GitHub',
        'description' => 'Every night, exports each n8n workflow to a GitHub repo as its own JSON file. Changes are detected by hashing the export the same way git does, so only workflows that actually changed are committed. Renames are handled, and failed writes can alert you in Slack, email or a sheet.',
        'tags' => ['n8n', 'GitHub', 'Backups'],
        'cover_image' => 'back-up-n8n-workflows-to-github.webp',
    ],
    [
        'slug' => 'weekly-client-updates-from-git',
        'title' => 'Weekly Client Updates from Git',
        'description' => 'Every Monday, reads the past week of commits on each client project, drops merges, bot commits and internal work, and has AI turn the rest into a short, non-technical progress email. Quiet weeks send nothing, and review mode sends every email to you first.',
        'tags' => ['GitHub', 'OpenAI', 'Email'],
        'cover_image' => 'weekly-client-updates-from-git.webp',
    ],
    [
        'slug' => 'ai-assistant-core',
        'title' => 'AI Assistant Core',
        'description' => 'One AI agent with memory and more than fifteen tools: Google Calendar, weather, Slack, web search, Google Maps lead scraping, lead and job queues, and journaling. Every chat channel, from Telegram to voice, calls this same core, so the assistant behaves the same everywhere.',
        'tags' => ['AI agent', 'OpenAI', 'Redis', 'Google Calendar', 'Slack'],
        'cover_image' => 'ai-assistant-core.webp',
    ],
    [
        'slug' => 'telegram-voice-and-text-agent',
        'title' => 'Telegram Voice and Text Agent',
        'description' => 'The front door to the assistant. It takes typed or spoken Telegram messages, transcribes voice with Whisper, routes commands to the right sub-workflow and replies in kind: text for text, and natural speech for voice.',
        'tags' => ['Telegram', 'Whisper', 'Text to speech', 'AI agent'],
        'cover_image' => 'telegram-voice-and-text-agent.webp',
    ],
    [
        'slug' => 'ocr-engine-evaluation',
        'title' => 'OCR Engine Evaluation',
        'description' => 'Runs the same documents through four text extraction engines (built-in PDF extraction, PaddleOCR, LlamaParse and Docling) and scores each result with an AI judge using n8n Evaluations, so the engine for a document pipeline is chosen on evidence rather than guesswork.',
        'tags' => ['OCR', 'n8n Evaluations', 'OpenAI', 'Google Drive'],
        'cover_image' => 'ocr-engine-evaluation.webp',
    ],
    [
        'slug' => 'daily-journal-to-obsidian',
        'title' => 'Daily Journal to Obsidian',
        'description' => 'Appends entries to the day\'s Markdown note in an Obsidian vault, creating the note if it does not exist yet. The Telegram agent calls it, so a voice note on a walk becomes a journal entry.',
        'tags' => ['Obsidian', 'Markdown', 'Telegram'],
        'cover_image' => 'daily-journal-to-obsidian.webp',
    ],
];
