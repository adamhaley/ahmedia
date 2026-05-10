# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a frontend web application for AH Media.ai that provides:
- File upload functionality with drag-and-drop interface
- Real-time chat interface powered by n8n webhooks
- Animated landing page with GSAP animations
- Demo content generation for recipes and dietary guidelines

## Architecture

### Frontend Structure
- **index.html**: Main landing page with file upload and chat interface
- **animation.js**: GSAP-powered animations for landing page effects and scroll triggers
- **chat.js**: Chat functionality with webhook integration for real-time messaging
- **styles.css**: CSS styling for the entire application

### Backend Integration
- **PHP webhook proxy**: Browser traffic goes through local PHP endpoints that forward to n8n
  - Upload proxy: `/api/upload.php`
  - Chat proxy: `/api/chat.php`
  - Shared secret loaded from `.env` via `N8N_WEBHOOK_KEY`
  - Optional upstream overrides: `N8N_CHAT_WEBHOOK_URL`, `N8N_UPLOAD_WEBHOOK_URL`

### Demo Content System
- **demo/recipes.py**: Generates sample recipe markdown files with dietary metadata
- **demo/diets.py**: Creates dietary guideline content (paleo, gluten-free, vegan, keto, whole30, nut-free, low-FODMAP)
- **cartesia.sh**: Text-to-speech integration script using Cartesia API

## Key Features

### File Upload System
- Drag-and-drop interface with visual feedback
- Progress indicators for file uploads
- Namespace-based file organization (stored in localStorage)
- Supports multiple file uploads simultaneously

### Chat Interface
- Real-time messaging with typing indicators
- Collapsible chat container with reopen functionality
- Message formatting with HTML parsing
- Namespace-aware conversations

### Animation System
- GSAP-powered scroll-triggered animations
- Animated service cards with hover effects
- Coordinated circle and line animations on scroll
- Responsive design elements

## Development Notes

### Dependencies
- **GSAP**: Animation library loaded via CDN
- **Font Awesome**: Icons loaded via CDN
- **n8n**: Backend automation platform for webhook processing

### File Organization
- All demo content generated in `demo/` subdirectories
- Recipe files: `demo/recipes/md/` and `demo/recipes/pdf/`
- Diet guidelines: `demo/guidelines/md/` and `demo/guidelines/pdf/`

### Environment Variables
- `CARTESIA_API_KEY`: Required for text-to-speech functionality in cartesia.sh
- `N8N_WEBHOOK_KEY`: Required for authenticating proxy requests to n8n
- `N8N_CHAT_WEBHOOK_URL`: Optional override for the upstream chat webhook
- `N8N_UPLOAD_WEBHOOK_URL`: Optional override for the upstream upload webhook

## Webhook Configuration

The browser should call local proxy endpoints instead of hitting n8n directly:
- `/api/chat.php` forwards JSON chat requests
- `/api/upload.php` forwards multipart file uploads
- n8n should validate the `X-Webhook-Key` header on both workflows

## Browser Compatibility

The application uses modern JavaScript features including:
- Async/await for API calls
- Fetch API for HTTP requests
- DOM manipulation with modern selectors
- localStorage for client-side persistence
