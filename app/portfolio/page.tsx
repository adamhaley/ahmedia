'use client'

import { useEffect, useState } from 'react'
import gsap from 'gsap'
import { portfolioData } from '@/lib/portfolio-data'

export default function Portfolio() {
  const [expandedProject, setExpandedProject] = useState<string | null>(null)

  useEffect(() => {
    gsap.from('.portfolio-card', {
      duration: 0.8,
      opacity: 0,
      y: 50,
      stagger: 0.12,
      ease: 'power3.out'
    })
  }, [])

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'active':
        return 'bg-green-500/20 text-green-400 border-green-500/30'
      case 'in-development':
        return 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30'
      case 'completed':
        return 'bg-neon-blue/20 text-neon-blue border-neon-blue/30'
      default:
        return 'bg-neon-blue/20 text-neon-blue border-neon-blue/30'
    }
  }

  const formatDateRange = (dateRange: any) => {
    const started = new Date(dateRange.started).toLocaleDateString('en-US', {
      month: 'short',
      year: 'numeric'
    })

    if (dateRange.completed) {
      const completed = new Date(dateRange.completed).toLocaleDateString('en-US', {
        month: 'short',
        year: 'numeric'
      })
      return `${started} - ${completed}`
    }

    if (dateRange.last_updated) {
      return `${started} - Present`
    }

    return started
  }

  const getResultMetrics = (results: any) => {
    const metrics = []
    if (results.leads_generated) metrics.push(`${results.leads_generated.toLocaleString()} leads`)
    if (results.automation_hours_saved_per_week) metrics.push(`${results.automation_hours_saved_per_week}hrs/week saved`)
    if (results.beta_users) metrics.push(`${results.beta_users} beta users`)
    if (results.accounts_connected) metrics.push(`${results.accounts_connected} accounts`)
    if (results.reports_generated) metrics.push(`${results.reports_generated} reports`)
    if (results.books_processed) metrics.push(`${results.books_processed} books`)
    return metrics
  }

  return (
    <div className="min-h-screen py-20">
      <div className="container mx-auto px-6">
        {/* Header */}
        <div className="max-w-4xl mx-auto text-center mb-16">
          <h1 className="text-5xl md:text-6xl font-bold text-white mb-6">
            Project <span className="text-neon-blue neon-text">Portfolio</span>
          </h1>
          <p className="text-xl text-white/70">
            End-to-end AI automation systems designed, built, and deployed by AHMedia.ai
          </p>
        </div>

        {/* Portfolio Grid */}
        <div className="grid lg:grid-cols-2 gap-6 max-w-7xl mx-auto">
          {portfolioData.map((project) => (
            <div
              key={project.id}
              className="portfolio-card border border-neon-blue/30 rounded-lg hover:border-neon-blue/60 transition-all duration-300 hover:shadow-[0_0_30px_rgba(0,255,255,0.2)] bg-dark-bg/30 overflow-hidden"
            >
              <div className="p-6">
                {/* Header with badges */}
                <div className="flex items-start justify-between mb-4 gap-2 flex-wrap">
                  <div className="flex gap-2 flex-wrap">
                    <span className={`px-3 py-1 text-xs font-semibold rounded-full border ${getStatusColor(project.status)}`}>
                      {project.status}
                    </span>
                    <span className="px-3 py-1 text-xs bg-neon-blue/10 text-neon-blue/70 rounded-full border border-neon-blue/20">
                      {project.category}
                    </span>
                  </div>
                </div>

                {/* Title and Client */}
                <h3 className="text-2xl font-bold text-white mb-2">{project.title}</h3>
                {project.client && (
                  <p className="text-sm text-neon-blue/70 mb-3">Client: {project.client}</p>
                )}

                {/* Overview */}
                <p className="text-white/70 mb-4 text-sm leading-relaxed">{project.overview}</p>

                {/* Tech Stack */}
                <div className="mb-4">
                  <h4 className="text-xs font-bold text-neon-blue/80 mb-2 uppercase tracking-wide">Stack</h4>
                  <div className="flex flex-wrap gap-1.5">
                    {project.stack.map((tech, techIndex) => (
                      <span
                        key={techIndex}
                        className="px-2 py-1 text-xs bg-dark-bg/80 border border-neon-blue/20 text-white/60 rounded"
                      >
                        {tech}
                      </span>
                    ))}
                  </div>
                </div>

                {/* Key Features - Expandable */}
                {project.key_features.length > 0 && (
                  <div className="mb-4">
                    <button
                      onClick={() => setExpandedProject(expandedProject === project.id ? null : project.id)}
                      className="text-xs font-bold text-neon-blue/80 mb-2 uppercase tracking-wide hover:text-neon-blue transition-colors flex items-center gap-1"
                    >
                      Key Features
                      <svg
                        className={`w-4 h-4 transition-transform ${expandedProject === project.id ? 'rotate-180' : ''}`}
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                      </svg>
                    </button>
                    {expandedProject === project.id && (
                      <ul className="space-y-1.5 pl-3 border-l-2 border-neon-blue/30">
                        {project.key_features.map((feature, idx) => (
                          <li key={idx} className="text-xs text-white/60 leading-relaxed">
                            • {feature}
                          </li>
                        ))}
                      </ul>
                    )}
                  </div>
                )}

                {/* Results & Metrics */}
                <div className="pt-4 border-t border-neon-blue/20 space-y-3">
                  {getResultMetrics(project.results).length > 0 && (
                    <div className="flex flex-wrap gap-2">
                      {getResultMetrics(project.results).map((metric, idx) => (
                        <span key={idx} className="text-xs font-semibold text-neon-blue">
                          {metric}
                        </span>
                      ))}
                    </div>
                  )}

                  <p className="text-sm text-white/80 italic">
                    "{project.results.impact_summary}"
                  </p>

                  <p className="text-xs text-white/40">
                    {formatDateRange(project.date_range)}
                  </p>
                </div>
              </div>
            </div>
          ))}
        </div>

        {/* Stats Section */}
        <div className="max-w-4xl mx-auto mt-20 p-8 border border-neon-blue/30 rounded-lg bg-dark-bg/20">
          <div className="grid md:grid-cols-3 gap-8 text-center">
            <div>
              <div className="text-4xl font-bold text-neon-blue mb-2">
                {portfolioData.length}
              </div>
              <div className="text-white/60">Projects Delivered</div>
            </div>
            <div>
              <div className="text-4xl font-bold text-neon-blue mb-2">
                {portfolioData.filter(p => p.status === 'active').length}
              </div>
              <div className="text-white/60">Active Systems</div>
            </div>
            <div>
              <div className="text-4xl font-bold text-neon-blue mb-2">
                {new Set(portfolioData.flatMap(p => p.stack)).size}
              </div>
              <div className="text-white/60">Technologies Used</div>
            </div>
          </div>
        </div>

        {/* CTA Section */}
        <div className="max-w-4xl mx-auto text-center mt-20">
          <h2 className="text-3xl md:text-4xl font-bold text-white mb-6">
            Ready to Build Your Next AI System?
          </h2>
          <p className="text-lg text-white/70 mb-8">
            Let's discuss how we can automate and enhance your business processes
          </p>
          <a
            href="mailto:contact@ahmedia.ai"
            className="inline-block px-8 py-4 bg-neon-blue text-dark-bg font-bold rounded-lg hover:bg-white transition-all duration-300 hover:shadow-[0_0_20px_rgba(0,255,255,0.5)]"
          >
            Get In Touch
          </a>
        </div>
      </div>
    </div>
  )
}
