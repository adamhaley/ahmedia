'use client'

import { useEffect } from 'react'
import gsap from 'gsap'

export default function Services() {
  useEffect(() => {
    gsap.from('.service-card', {
      duration: 0.8,
      opacity: 0,
      y: 50,
      stagger: 0.2,
      ease: 'power3.out'
    })
  }, [])

  const services = [
    {
      icon: '🤖',
      title: 'AI Strategy & Consulting',
      description: 'Strategic guidance on AI implementation and integration into your business processes',
      features: [
        'AI readiness assessment',
        'Technology roadmap development',
        'ROI analysis and planning',
        'Team training and enablement'
      ]
    },
    {
      icon: '⚙️',
      title: 'Custom AI Solutions',
      description: 'Bespoke AI applications tailored to your specific business needs and workflows',
      features: [
        'Natural language processing',
        'Computer vision applications',
        'Predictive analytics',
        'Custom model development'
      ]
    },
    {
      icon: '🔄',
      title: 'Process Automation',
      description: 'Intelligent automation solutions that streamline operations and reduce manual work',
      features: [
        'Workflow automation',
        'Document processing',
        'Intelligent chatbots',
        'Integration with existing systems'
      ]
    },
    {
      icon: '📊',
      title: 'Data Intelligence',
      description: 'Transform your data into actionable insights with advanced AI analytics',
      features: [
        'Data analysis and visualization',
        'Pattern recognition',
        'Anomaly detection',
        'Predictive modeling'
      ]
    },
    {
      icon: '🔌',
      title: 'API Integration',
      description: 'Seamless integration of AI capabilities into your existing applications',
      features: [
        'RESTful API development',
        'Third-party AI service integration',
        'Microservices architecture',
        'Real-time data processing'
      ]
    },
    {
      icon: '🛠️',
      title: 'Ongoing Support',
      description: 'Continuous optimization and support to ensure your AI solutions deliver value',
      features: [
        'Performance monitoring',
        'Model retraining and updates',
        'Technical support',
        'Feature enhancements'
      ]
    }
  ]

  return (
    <div className="min-h-screen py-20">
      <div className="container mx-auto px-6">
        {/* Header */}
        <div className="max-w-4xl mx-auto text-center mb-16">
          <h1 className="text-5xl md:text-6xl font-bold text-white mb-6">
            Our <span className="text-neon-blue neon-text">Services</span>
          </h1>
          <p className="text-xl text-white/70">
            Comprehensive AI solutions designed to transform your business operations
          </p>
        </div>

        {/* Services Grid */}
        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
          {services.map((service, index) => (
            <div
              key={index}
              className="service-card p-8 border border-neon-blue/30 rounded-lg hover:border-neon-blue/60 transition-all duration-300 hover:shadow-[0_0_30px_rgba(0,255,255,0.2)] bg-dark-bg/30"
            >
              <div className="text-5xl mb-4">{service.icon}</div>
              <h3 className="text-2xl font-bold text-white mb-3">{service.title}</h3>
              <p className="text-white/60 mb-6">{service.description}</p>

              <ul className="space-y-2">
                {service.features.map((feature, featureIndex) => (
                  <li key={featureIndex} className="flex items-start gap-2 text-white/70">
                    <span className="text-neon-blue mt-1">•</span>
                    <span>{feature}</span>
                  </li>
                ))}
              </ul>
            </div>
          ))}
        </div>

        {/* CTA Section */}
        <div className="max-w-4xl mx-auto text-center mt-20">
          <h2 className="text-3xl md:text-4xl font-bold text-white mb-6">
            Ready to Get Started?
          </h2>
          <p className="text-lg text-white/70 mb-8">
            Contact us to discuss how we can help transform your business with AI
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
