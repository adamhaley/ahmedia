# AH Media.ai - Next.js Application

Modern Next.js rebuild of the AH Media.ai website featuring:
- Next.js 15 with App Router & TypeScript
- Supabase integration (authentication & database)
- GSAP animations with ScrollTrigger
- Tailwind CSS with custom neon design system
- RAG demo with file upload and chat interface

## Getting Started

### Install Dependencies

```bash
npm install
```

### Configure Environment (Optional)

Update `.env.local` with your Supabase credentials if you plan to use authentication/database features.

### Run Development Server

```bash
npm run dev
```

Visit [http://localhost:3000](http://localhost:3000)

### Build for Production

```bash
npm run build
npm start
```

## Project Structure

```
├── app/                    # Next.js pages (App Router)
│   ├── page.tsx           # Home page with hero
│   ├── services/          # Services page
│   ├── portfolio/         # Portfolio showcase
│   ├── demo/              # RAG demo
│   └── layout.tsx         # Root layout with nav
├── components/
│   ├── ui/               # Navigation
│   ├── animations/       # Circles, Lines (GSAP)
│   └── demo/             # FileUpload, ChatInterface
└── lib/                  # Supabase client
```

## Features

### Pages
- **Home**: Animated hero with service overview
- **Services**: 6 AI service offerings (placeholder content - replace with yours)
- **Portfolio**: Project showcase (placeholder content - replace with yours)
- **Demo**: Interactive RAG with file upload and chat

### Design System
- **Colors**: Neon cyan (#00ffff) on dark teal (#005e5e)
- **Animations**: GSAP scroll-triggered effects, pulsing circles/lines
- **Typography**: Clean, bold headings with neon glow effects

## Customization

### Add Your Content

1. **Services**: Edit `app/services/page.tsx` - replace the `services` array
2. **Portfolio**: Edit `app/portfolio/page.tsx` - replace the `projects` array
3. **Home**: Edit `app/page.tsx` - update hero text and features

### Update Webhooks

The demo uses n8n webhooks. Update URLs in:
- `components/demo/FileUpload.tsx` (line ~50)
- `components/demo/ChatInterface.tsx` (line ~40)

## Tech Stack

- Next.js 15 • TypeScript • Tailwind CSS • GSAP • Supabase • n8n

## Deployment

Deploy to Vercel (recommended):

```bash
npm install -g vercel
vercel
```

Or build and deploy the `.next` folder to any Node.js hosting platform.

## Migration Notes

This is a complete rebuild of the original vanilla HTML/CSS/JS site. All animations and visual design elements have been preserved while improving:
- Scalability with React components
- Type safety with TypeScript
- Better routing with Next.js App Router
- Easier content management
