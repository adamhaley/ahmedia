export interface PortfolioProject {
  id: string
  title: string
  category: string
  status: 'active' | 'in-development' | 'completed'
  client?: string
  stack: string[]
  overview: string
  key_features: string[]
  results: {
    leads_generated?: number
    automation_hours_saved_per_week?: number
    beta_users?: number
    accounts_connected?: number
    reports_generated?: number
    books_processed?: number
    avg_processing_time_minutes?: number
    impact_summary: string
  }
  media?: {
    thumbnail?: string
  }
  date_range: {
    started: string
    last_updated?: string
    completed?: string
  }
}

export const portfolioData: PortfolioProject[] = [
  {
    id: "finder-felix",
    title: "Finder Felix",
    category: "Automation System",
    status: "active",
    client: "Deutsches Edelsteinhaus",
    stack: ["n8n", "Supabase", "Firecrawl", "Outscraper", "Truelist", "Instantly.ai"],
    overview: "Lead generation automation that scrapes German business directories and enriches clinic data, syncing to Supabase and orchestrating personalized outreach sequences.",
    key_features: [
      "Automated web scraping from GelbeSeiten and Google Maps",
      "Data enrichment with Firecrawl and Truelist",
      "Lead scoring and deduplication in Supabase",
      "Instantly.ai integration for DKIM-verified outreach campaigns"
    ],
    results: {
      leads_generated: 12000,
      automation_hours_saved_per_week: 40,
      impact_summary: "Enabled fully automated daily lead enrichment and outreach for German dental clinics."
    },
    media: {
      thumbnail: "https://ahmedia.ai/images/finder-felix-thumb.jpg"
    },
    date_range: {
      started: "2024-02",
      last_updated: "2025-09"
    }
  },
  {
    id: "analysis-anna",
    title: "Analysis Anna",
    category: "AI Data Assistant",
    status: "active",
    client: "Deutsches Edelsteinhaus",
    stack: ["n8n", "OpenAI", "Supabase"],
    overview: "LLM-powered analyst that reviews newly scraped leads from Finder Felix, evaluates their online presence, and writes concise German business intelligence summaries.",
    key_features: [
      "Automated insight generation using GPT models",
      "Integration with Finder Felix Supabase tables",
      "Scored summaries stored in structured JSON for downstream automations"
    ],
    results: {
      impact_summary: "Replaced manual lead analysis with instant AI summaries across thousands of records."
    },
    date_range: {
      started: "2024-03",
      last_updated: "2025-09"
    }
  },
  {
    id: "pitch-paul",
    title: "Pitch Paul",
    category: "AI Content Generator",
    status: "active",
    client: "Deutsches Edelsteinhaus",
    stack: ["n8n", "OpenAI", "Supabase", "Instantly.ai"],
    overview: "Personalized outreach message generator that drafts multilingual cold emails for dental leads, leveraging profile data from Finder Felix and Analysis Anna.",
    key_features: [
      "Dynamic email tone and personalization engine",
      "AI-generated bilingual (German/English) content",
      "Integration with Instantly.ai campaign system"
    ],
    results: {
      impact_summary: "Automated production of unique, context-aware outreach emails for 1,000+ contacts weekly."
    },
    date_range: {
      started: "2024-04",
      last_updated: "2025-09"
    }
  },
  {
    id: "chad-fitness-coach",
    title: "Chad Fitness Coach",
    category: "Voice AI Assistant",
    status: "in-development",
    client: "Chad Fitness",
    stack: ["n8n", "Supabase", "Telegram API", "Whisper", "Cartesia TTS", "Ollama"],
    overview: "Voice-driven AI fitness coach that logs workouts, hydration, and discipline habits through Telegram voice commands and generates motivational summaries.",
    key_features: [
      "Speech-to-text via Whisper and text-to-speech via Cartesia",
      "Supabase daily logs and embedding summaries",
      "Telegram voice interaction for hands-free coaching",
      "Daily progress and accountability reports"
    ],
    results: {
      beta_users: 10,
      impact_summary: "Prototype voice-based fitness coach improving daily consistency through automation."
    },
    media: {
      thumbnail: "https://ahmedia.ai/images/chad-fitness-thumb.jpg"
    },
    date_range: {
      started: "2024-10",
      last_updated: "2025-10"
    }
  },
  {
    id: "lovable-ai-dashboard",
    title: "Lovable.ai Marketing Dashboard",
    category: "Dashboard & Reporting",
    status: "completed",
    client: "Brandli House AI",
    stack: ["Next.js", "Supabase", "Make.com", "Google Ads API", "Meta Ads API"],
    overview: "Cross-platform marketing dashboard aggregating ad campaign performance data and AI-generated insights for marketing agencies.",
    key_features: [
      "OAuth integrations with Google and Meta Ads",
      "Supabase-backed unified analytics schema",
      "AI-written campaign insights and optimization tips",
      "Multi-tenant access with secure RLS policies"
    ],
    results: {
      accounts_connected: 24,
      reports_generated: 320,
      impact_summary: "Reduced manual report prep from 3 hours to 10 minutes per client."
    },
    date_range: {
      started: "2025-01",
      completed: "2025-07"
    }
  },
  {
    id: "megyk-book-summaries",
    title: "Megyk Book Summaries",
    category: "AI Content Engine",
    status: "active",
    stack: ["n8n", "Supabase", "Ollama", "LangChain", "Python", "Whisper", "ReportLab"],
    overview: "Automated pipeline for ingesting, embedding, and summarizing eBooks into personalized study workbooks with custom formatting and RAG-based insights.",
    key_features: [
      "PDF and MOBI ingestion with chapter and chunk splitting",
      "Embeddings stored in Supabase using pgvector",
      "LLM-generated chapter summaries and Q&A workbook outputs",
      "Automated PDF generation using ReportLab templates"
    ],
    results: {
      books_processed: 15,
      avg_processing_time_minutes: 12,
      impact_summary: "Transforms large books into concise, interactive AI-driven study guides."
    },
    media: {
      thumbnail: "https://ahmedia.ai/images/megyk-thumb.jpg"
    },
    date_range: {
      started: "2024-08",
      last_updated: "2025-10"
    }
  },
  {
    id: "rag-chef-demo",
    title: "Private Recipe RAG Chatbot Demo",
    category: "RAG Chatbot",
    status: "completed",
    stack: ["LangChain", "Ollama", "Supabase", "Next.js"],
    overview: "Demonstration of private knowledge retrieval using locally stored recipes and embeddings, showing how proprietary data can remain private while enabling dietary Q&A.",
    key_features: [
      "RAG (Retrieval-Augmented Generation) architecture",
      "Obfuscated recipe dataset for testing knowledge boundaries",
      "Embeddings and vector search via Supabase pgvector",
      "Next.js interface for conversational demo"
    ],
    results: {
      impact_summary: "Showcased privacy-preserving RAG techniques for prospective clients and workshops."
    },
    date_range: {
      started: "2024-05",
      completed: "2024-06"
    }
  },
  {
    id: "smart-website-template",
    title: "Smart Website Boilerplate",
    category: "Creative Tool",
    status: "active",
    stack: ["HTML", "CSS Grid", "JavaScript", "n8n", "Supabase"],
    overview: "Responsive artistic website starter template inspired by CSS Zen Garden, designed for brochure-style AI-enhanced websites with structured HTML and dynamic components.",
    key_features: [
      "Structured semantic HTML and modular CSS Grid layouts",
      "AI chatbot integration via Supabase RAG connector",
      "Customizable visual design and animation presets",
      "Designed for boutique businesses and creative professionals"
    ],
    results: {
      impact_summary: "Reusable front-end base for AHMedia client projects with AI interactivity."
    },
    date_range: {
      started: "2025-05",
      last_updated: "2025-10"
    }
  },
  {
    id: "ahmedia-infra-stack",
    title: "AHMedia Infrastructure Stack",
    category: "DevOps & Hosting",
    status: "active",
    stack: ["DigitalOcean", "Docker Compose", "Caddy", "DirectAdmin", "Supabase", "Ollama", "RunPod", "n8n"],
    overview: "Custom-built infrastructure powering AHMedia's automation ecosystem, including n8n orchestrators, Supabase databases, local Ollama LLMs, and AI dashboard deployments.",
    key_features: [
      "Multi-droplet DigitalOcean environment with isolated services",
      "Docker Compose networking with shared Caddy reverse proxy",
      "Self-hosted Ollama for local embeddings and inference",
      "Automated backup and mail relay with DKIM/SPF/DMARC setup"
    ],
    results: {
      impact_summary: "Scalable, secure foundation for multi-tenant AI automation projects."
    },
    date_range: {
      started: "2023-11",
      last_updated: "2025-10"
    }
  }
]
