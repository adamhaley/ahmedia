<?php

declare(strict_types=1);

/**
 * AH Media's starting FAQs, used by both the /faq/ page and the chatbot.
 *
 * `keywords` are the alternate phrasings a visitor might type (nobody types
 * "What does a website cost?", they type "how much for a site"). They weigh
 * heavily in App\Services\Faq\FaqMatchingService. Pricing mirrors the second
 * brain (wiki/projects/ahmedia/reference.md), which stays the source of
 * truth; after seeding, edit FAQs in the admin rather than here.
 *
 * @return list<array{question: string, answer: string, keywords: string}>
 */
return [
    [
        'question' => 'What does AH Media do?',
        'answer' => '<p>AH Media designs, builds and ships the AI systems businesses actually run on: custom chatbots and assistants trained on your own content, automation that connects the tools you already use (often with n8n), custom web apps, websites and AI voice receptionists.</p><p>We also offer fixed-price packages for securing vibe-coded apps before launch, recovering hacked sites, and making AI-built sites look like your brand.</p>',
        'keywords' => 'services, what do you do, what you offer, offerings, help with, capabilities, about, who are you',
    ],
    [
        'question' => 'Who is behind AH Media?',
        'answer' => '<p>AH Media is built and run by Adam Haley, a senior developer with more than 20 years of experience building production software, including for companies like Disney, Sony and Apple. You work directly with the person building your system: no account managers and no hand-offs.</p>',
        'keywords' => 'adam, owner, founder, team, who runs, experience, background, about you, agency, freelancer',
    ],
    [
        'question' => 'How much do you charge?',
        'answer' => '<p>Custom AI, automation and programming work is $95 an hour, or on a monthly retainer: $1,000 a month for up to 10 hours, or $2,000 a month for up to 25 hours.</p><p>Websites, voice receptionists and the fixed-price packages have their own set prices. See the Services pages for each one.</p>',
        'keywords' => 'price, pricing, cost, rate, hourly, how much, budget, fees, charge, quote, expensive, cheap, per hour',
    ],
    [
        'question' => 'Do you offer monthly retainers?',
        'answer' => '<p>Yes. The Starter retainer is $1,000 a month for up to 10 hours of custom automation or consulting work, and the Growth retainer is $2,000 a month for up to 25 hours. Retainers suit ongoing improvements and support after a project launches.</p>',
        'keywords' => 'retainer, monthly, ongoing, support, maintenance, subscription, after launch, continued help, long term',
    ],
    [
        'question' => 'How much does a website cost?',
        'answer' => '<p>Websites come in four tiers, each with a $20 a month hosting and maintenance fee:</p><ul><li>$500: a one-page brochure site, SEO-optimized, with a contact form</li><li>$1,500: a multi-page site plus a basic chatbot trained on your company data and one automation</li><li>$2,500: up to 10 pages, a CMS you can edit yourself, custom design, chatbot and up to two automations</li><li>$5,000: 10+ pages, fully dynamic, product catalogue or e-commerce if needed, and up to three automations</li></ul>',
        'keywords' => 'website, website cost, website price, how much for a website, web site, web design, landing page, new site, build a site, ecommerce, online store, pages, cms',
    ],
    [
        'question' => 'Can you build a chatbot for my business?',
        'answer' => '<p>Yes. We build chatbots and assistants trained on your own content: FAQs, documents, product information or your knowledge base. Depending on what you need, that can be a simple, low-cost FAQ bot (like this one) or a full AI assistant that searches your documents and writes its own answers.</p>',
        'keywords' => 'chatbot, chat bot, make a chatbot, company chatbot, business chatbot, bot, assistant, ai chat, rag, knowledge base, customer support, answer questions, gpt, claude, company',
    ],
    [
        'question' => 'What is an AI voice receptionist?',
        'answer' => '<p>A real phone line answered by an AI receptionist trained on your business. It handles calls, answers common questions and takes messages or bookings, with the conversation flow and voice designed around how you work.</p><p>Set-up is $950, then $500 a month.</p>',
        'keywords' => 'phone, voice, receptionist, calls, answering service, voice ai, call answering, bookings, appointments, missed calls',
    ],
    [
        'question' => 'What can you automate?',
        'answer' => '<p>Most of the repetitive work between the tools you already use: qualifying and routing leads, drafting email replies, syncing data between apps, reports, onboarding steps and notifications. We usually build automation with n8n, which connects to hundreds of services, plus custom code where it earns its keep.</p>',
        'keywords' => 'automation, automate, workflow, n8n, zapier, make, integrations, connect apps, repetitive tasks, busywork, process',
    ],
    [
        'question' => 'What is the Brand Audit for AI-built sites?',
        'answer' => '<p>AI website builders make every site look the same. The Brand Audit reviews up to five pages across six layers (type, colour, hero, layout, header and icons, and copy), rates each one, and gives you a fix plan ranked by effort and impact.</p><p>It costs $500 and takes 5 days. Add implementation for $700 and we make the changes for you.</p>',
        'keywords' => 'brand audit, branding, generic, looks like ai, template, design review, fonts, colours, colors, redesign, stand out, unique',
    ],
    [
        'question' => 'What is the Vibe-Coded App Go-Live Review?',
        'answer' => '<p>If you built an app with an AI tool and are about to put real users on it, the 360° Go-Live Review checks every side of it first: security, data isolation, secrets, code quality, maintainability, performance, reliability and launch readiness. Every critical and high issue is fixed, and you get a plain-English report.</p><p>It costs $1,000 and takes 5 days. A codebase clean-up add-on is $750.</p>',
        'keywords' => 'vibe coded, vibe coding, lovable, bolt, cursor, replit, launch, go live, security review, app audit, ai built app, supabase, before launch',
    ],
    [
        'question' => 'My website has been hacked. Can you help?',
        'answer' => '<p>Yes. Hacked Site Recovery finds how they got in, removes the malware, backdoors and rogue accounts, hardens the site and asks browsers to lift any "deceptive site" warnings. You get a plain-English incident report and 30 days of cover if the same way in is used again.</p><p>It costs $1,000 with 3-day delivery. A rush 24-hour option is +$300, and a clean rebuild on fresh hosting is +$750. WordPress is the most common case, but we work on any site we can get server access to.</p>',
        'keywords' => 'hacked, hack, malware, virus, compromised, breach, red warning, deceptive site, wordpress, infected, spam, security, recovery',
    ],
    [
        'question' => 'What technology do you work with?',
        'answer' => '<p>Mostly Laravel and PHP, n8n for automation, Python, Node, Supabase and other Postgres or MySQL databases, and AI models from Anthropic (Claude) and OpenAI, including through AWS Bedrock. We pick the simplest tool that does the job well rather than the newest one.</p>',
        'keywords' => 'tech stack, technology, tools, laravel, php, python, node, supabase, openai, claude, aws, bedrock, framework, language',
    ],
    [
        'question' => "I'm not technical. Can I still work with you?",
        'answer' => '<p>Absolutely, and most clients aren\'t. We explain things in plain English, recommend what\'s worth building (and what isn\'t), and handle the technical side, including hosting if you don\'t have it.</p>',
        'keywords' => 'not technical, non technical, beginner, no experience, explain, simple, dont understand, small business, first time',
    ],
    [
        'question' => 'Can you set up hosting for me?',
        'answer' => '<p>Yes. If you don\'t have hosting yet, we recommend a home for your site or app and set it up, including the domain, SSL certificate, email and backups.</p>',
        'keywords' => 'hosting, host, server, domain, deploy, deployment, ssl, https, dns, where to host, go live, put online',
    ],
    [
        'question' => 'How do projects start?',
        'answer' => '<p>Send a few details through the contact form, or ask here in the chat. We start with a conversation about what you\'re building and the real bottleneck behind it, then propose an approach and a price before any work begins. Fixed-price packages can start as soon as you order.</p>',
        'keywords' => 'get started, start, next steps, process, how does it work, hire, book, consultation, discovery call, onboarding, contact',
    ],
    [
        'question' => 'How long does a project take?',
        'answer' => '<p>The fixed-price packages take 3 to 5 days. Websites and custom builds depend on scope: a simple site can be live within a week or two, while larger apps and automations are planned in stages, and you see working progress along the way.</p>',
        'keywords' => 'timeline, how long, turnaround, delivery, deadline, duration, fast, quickly, when, weeks, days',
    ],
];
