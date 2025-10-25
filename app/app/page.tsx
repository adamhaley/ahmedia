'use client'

import { useEffect } from 'react'
import gsap from 'gsap'
import AnimatedCircles from '@/components/animations/AnimatedCircles'
import Link from 'next/link'

export default function Home() {
  useEffect(() => {
    // Initial title animation
    gsap.from('.hero-title', {
      duration: 1.5,
      opacity: 0,
      y: 100,
      ease: 'power4.out'
    })

    gsap.from('.hero-subtitle', {
      duration: 1.5,
      opacity: 0,
      y: 50,
      delay: 0.3,
      ease: 'power4.out'
    })

    gsap.from('.hero-cta', {
      duration: 1.5,
      opacity: 0,
      y: 30,
      delay: 0.6,
      ease: 'power4.out'
    })
  }, [])

  return (
    <div className="min-h-screen relative overflow-hidden">
      {/* Hero Section */}
      <section className="relative h-screen flex items-center">
        <AnimatedCircles />

        <div className="container mx-auto px-6 relative z-10">
          <div className="max-w-4xl">
            <h1 className="hero-title text-6xl md:text-8xl font-bold mb-6">
              <span className="text-white">AH Media</span>
              <span className="text-neon-blue neon-text">.ai</span>
            </h1>

            <p className="hero-subtitle text-xl md:text-2xl text-white/80 mb-8 max-w-2xl">
              Your AI transformation partner. We build intelligent solutions that drive real business results.
            </p>

            <div className="hero-cta flex gap-4">
              <Link
                href="/services"
                className="px-8 py-4 bg-neon-blue text-dark-bg font-bold rounded-lg hover:bg-white transition-all duration-300 hover:shadow-[0_0_20px_rgba(0,255,255,0.5)]"
              >
                Our Services
              </Link>
              <Link
                href="/portfolio"
                className="px-8 py-4 border-2 border-neon-blue text-neon-blue font-bold rounded-lg hover:bg-neon-blue/10 transition-all duration-300"
              >
                View Portfolio
              </Link>
            </div>
          </div>
        </div>
      </section>

      {/* About Section */}
      <section className="relative py-20 bg-dark-bg/50">
        <div className="container mx-auto px-6">
          <div className="max-w-4xl mx-auto text-center">
            <h2 className="text-4xl md:text-5xl font-bold text-white mb-8">
              AI Solutions That <span className="text-neon-blue neon-text">Transform</span>
            </h2>

            <p className="text-lg text-white/70 mb-12">
              We specialize in implementing AI technologies that create measurable impact for businesses.
              From automation to intelligent decision-making systems, we make AI accessible and effective.
            </p>

            <div className="grid md:grid-cols-3 gap-8">
              {[
                {
                  title: 'Custom AI Solutions',
                  description: 'Tailored AI implementations designed for your specific business needs'
                },
                {
                  title: 'Process Automation',
                  description: 'Streamline operations with intelligent automation workflows'
                },
                {
                  title: 'Data Intelligence',
                  description: 'Extract insights and value from your data with AI-powered analytics'
                }
              ].map((feature, index) => (
                <div
                  key={index}
                  className="p-6 border border-neon-blue/20 rounded-lg hover:border-neon-blue/50 transition-all duration-300 hover:shadow-[0_0_20px_rgba(0,255,255,0.1)]"
                >
                  <h3 className="text-xl font-bold text-white mb-3">{feature.title}</h3>
                  <p className="text-white/60">{feature.description}</p>
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="relative py-20">
        <div className="container mx-auto px-6 text-center">
          <h2 className="text-3xl md:text-4xl font-bold text-white mb-6">
            Ready to Transform Your Business?
          </h2>
          <p className="text-lg text-white/70 mb-8">
            Try our interactive demo to see AI in action
          </p>
          <Link
            href="/demo"
            className="inline-block px-8 py-4 bg-neon-blue text-dark-bg font-bold rounded-lg hover:bg-white transition-all duration-300 hover:shadow-[0_0_20px_rgba(0,255,255,0.5)]"
          >
            Try Demo
          </Link>
        </div>
      </section>
    </div>
  )
}
