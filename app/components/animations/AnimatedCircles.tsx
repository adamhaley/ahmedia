'use client'

import { useEffect, useRef } from 'react'
import gsap from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'

gsap.registerPlugin(ScrollTrigger)

export default function AnimatedCircles() {
  const containerRef = useRef<HTMLDivElement>(null)

  useEffect(() => {
    if (!containerRef.current) return

    const circles = containerRef.current.querySelectorAll('.circle')

    // Initial animation
    gsap.from(circles, {
      duration: 1.5,
      scale: 0.4,
      opacity: 0,
      stagger: 0.2,
      ease: 'power4.out'
    })

    // Scroll-based animations
    circles.forEach((circle, index) => {
      const rotation = index === 0 ? 180 : index === 1 ? -180 : 360

      gsap.to(circle, {
        scrollTrigger: {
          trigger: containerRef.current,
          start: 'top top',
          end: 'bottom top',
          scrub: 1,
          onUpdate: (self) => {
            const scale = 0.4 + (self.progress * 0.4)
            ;(circle as HTMLElement).style.setProperty('--base-scale', scale.toString())
          }
        },
        rotation
      })
    })

    return () => {
      ScrollTrigger.getAll().forEach(trigger => trigger.kill())
    }
  }, [])

  return (
    <div ref={containerRef} className="absolute inset-0 pointer-events-none overflow-hidden">
      <div
        className="circle absolute rounded-full border-2 border-neon-blue mix-blend-screen"
        style={{
          width: '20vmin',
          height: '20vmin',
          top: '12%',
          right: '25%',
          boxShadow: '0 0 15px var(--neon-blue), inset 0 0 15px var(--neon-blue)',
          animation: 'circlePulse 4.2s ease-in-out infinite',
        }}
      />
      <div
        className="circle absolute rounded-full border-2 border-neon-blue mix-blend-screen"
        style={{
          width: '35vmin',
          height: '35vmin',
          top: '5%',
          right: '55%',
          boxShadow: '0 0 15px var(--neon-blue), inset 0 0 15px var(--neon-blue)',
          animation: 'circlePulse 4.8s ease-in-out infinite',
        }}
      />
      <div
        className="circle absolute rounded-full border-2 border-neon-blue mix-blend-screen"
        style={{
          width: '45vmin',
          height: '45vmin',
          top: '0%',
          right: '15%',
          boxShadow: '0 0 15px var(--neon-blue), inset 0 0 15px var(--neon-blue)',
          animation: 'circlePulse 4.5s ease-in-out infinite',
        }}
      />
    </div>
  )
}
