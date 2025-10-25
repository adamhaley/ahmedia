'use client'

import { useEffect } from 'react'
import gsap from 'gsap'

export default function Portfolio() {
  useEffect(() => {
    gsap.from('.portfolio-card', {
      duration: 0.8,
      opacity: 0,
      y: 50,
      stagger: 0.15,
      ease: 'power3.out'
    })
  }, [])

  // Placeholder projects - you'll replace these with real content
  const projects = [
    {
      title: 'AI-Powered Document Processing',
      category: 'Automation',
      description: 'Automated document extraction and processing system that reduced manual data entry by 90%',
      technologies: ['NLP', 'Computer Vision', 'Python', 'n8n'],
      impact: '90% reduction in processing time'
    },
    {
      title: 'Intelligent Customer Support Bot',
      category: 'Chatbot',
      description: 'Custom-trained chatbot that handles customer inquiries with 85% accuracy',
      technologies: ['GPT-4', 'RAG', 'Vector DB', 'API Integration'],
      impact: '70% reduction in support tickets'
    },
    {
      title: 'Predictive Analytics Dashboard',
      category: 'Data Intelligence',
      description: 'Real-time analytics platform that predicts customer behavior and market trends',
      technologies: ['Machine Learning', 'Python', 'React', 'PostgreSQL'],
      impact: '25% increase in conversion rates'
    },
    {
      title: 'Voice-to-Text Workflow Automation',
      category: 'Automation',
      description: 'Voice-activated system for hands-free data entry and task management',
      technologies: ['Speech Recognition', 'NLP', 'n8n', 'APIs'],
      impact: '60% faster task completion'
    },
    {
      title: 'AI Content Generation Platform',
      category: 'Content',
      description: 'Automated content creation system for marketing materials and social media',
      technologies: ['GPT-4', 'DALL-E', 'Custom APIs', 'CMS Integration'],
      impact: '10x content production speed'
    },
    {
      title: 'Fraud Detection System',
      category: 'Security',
      description: 'Machine learning model that identifies fraudulent transactions in real-time',
      technologies: ['TensorFlow', 'Python', 'Real-time Processing'],
      impact: '95% fraud detection accuracy'
    }
  ]

  return (
    <div className="min-h-screen py-20">
      <div className="container mx-auto px-6">
        {/* Header */}
        <div className="max-w-4xl mx-auto text-center mb-16">
          <h1 className="text-5xl md:text-6xl font-bold text-white mb-6">
            Our <span className="text-neon-blue neon-text">Portfolio</span>
          </h1>
          <p className="text-xl text-white/70">
            Real-world AI implementations that deliver measurable results
          </p>
        </div>

        {/* Note about placeholder content */}
        <div className="max-w-4xl mx-auto mb-12 p-6 border border-neon-blue/20 rounded-lg bg-dark-bg/30 text-center">
          <p className="text-white/60">
            <span className="text-neon-blue font-bold">Note:</span> These are example projects.
            Add your actual portfolio items to showcase your work.
          </p>
        </div>

        {/* Portfolio Grid */}
        <div className="grid md:grid-cols-2 gap-8 max-w-6xl mx-auto">
          {projects.map((project, index) => (
            <div
              key={index}
              className="portfolio-card p-8 border border-neon-blue/30 rounded-lg hover:border-neon-blue/60 transition-all duration-300 hover:shadow-[0_0_30px_rgba(0,255,255,0.2)] bg-dark-bg/30"
            >
              <div className="flex items-start justify-between mb-4">
                <span className="px-3 py-1 text-sm bg-neon-blue/20 text-neon-blue rounded-full border border-neon-blue/30">
                  {project.category}
                </span>
              </div>

              <h3 className="text-2xl font-bold text-white mb-3">{project.title}</h3>
              <p className="text-white/70 mb-6">{project.description}</p>

              <div className="mb-4">
                <h4 className="text-sm font-bold text-neon-blue mb-2">Technologies Used:</h4>
                <div className="flex flex-wrap gap-2">
                  {project.technologies.map((tech, techIndex) => (
                    <span
                      key={techIndex}
                      className="px-2 py-1 text-xs bg-dark-bg border border-neon-blue/20 text-white/70 rounded"
                    >
                      {tech}
                    </span>
                  ))}
                </div>
              </div>

              <div className="pt-4 border-t border-neon-blue/20">
                <p className="text-sm text-neon-blue font-bold">
                  Impact: <span className="text-white/80 font-normal">{project.impact}</span>
                </p>
              </div>
            </div>
          ))}
        </div>

        {/* CTA Section */}
        <div className="max-w-4xl mx-auto text-center mt-20">
          <h2 className="text-3xl md:text-4xl font-bold text-white mb-6">
            Want to See More?
          </h2>
          <p className="text-lg text-white/70 mb-8">
            Get in touch to learn more about our projects and how we can help your business
          </p>
          <a
            href="mailto:contact@ahmedia.ai"
            className="inline-block px-8 py-4 bg-neon-blue text-dark-bg font-bold rounded-lg hover:bg-white transition-all duration-300 hover:shadow-[0_0_20px_rgba(0,255,255,0.5)]"
          >
            Contact Us
          </a>
        </div>
      </div>
    </div>
  )
}
