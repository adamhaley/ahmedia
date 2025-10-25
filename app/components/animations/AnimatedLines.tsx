'use client'

import { useEffect, useRef } from 'react'
import gsap from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'

gsap.registerPlugin(ScrollTrigger)

export default function AnimatedLines() {
  const upperRef = useRef<HTMLDivElement>(null)
  const lowerRef = useRef<HTMLDivElement>(null)

  useEffect(() => {
    if (!upperRef.current || !lowerRef.current) return

    const upperLines = upperRef.current.querySelectorAll('.line')
    const lowerLines = lowerRef.current.querySelectorAll('.line')

    // Animate upper lines
    gsap.to(upperLines[0], {
      scrollTrigger: {
        trigger: 'body',
        start: 'top top',
        end: 'bottom top',
        scrub: 1
      },
      scaleX: 0,
      transformOrigin: 'right',
      opacity: 0.2
    })

    gsap.to(upperLines[1], {
      scrollTrigger: {
        trigger: 'body',
        start: 'top top',
        end: 'bottom top',
        scrub: 1.5
      },
      scaleX: 0,
      transformOrigin: 'right',
      opacity: 0.2
    })

    // Animate lower lines
    gsap.to(lowerLines[0], {
      scrollTrigger: {
        trigger: 'body',
        start: 'top top',
        end: 'bottom top',
        scrub: 1
      },
      scaleX: 0,
      transformOrigin: 'left',
      opacity: 0.2
    })

    gsap.to(lowerLines[1], {
      scrollTrigger: {
        trigger: 'body',
        start: 'top top',
        end: 'bottom top',
        scrub: 1.5
      },
      scaleX: 0,
      transformOrigin: 'left',
      opacity: 0.2
    })

    return () => {
      ScrollTrigger.getAll().forEach(trigger => trigger.kill())
    }
  }, [])

  return (
    <>
      {/* Upper right lines */}
      <div ref={upperRef} className="absolute top-0 right-0 z-0">
        <div
          className="line absolute h-0.5 bg-neon-blue opacity-40"
          style={{
            width: '150px',
            right: 0,
            top: '20px',
            boxShadow: '0 0 10px var(--neon-blue)',
            animation: 'pulse 1.8s ease-in-out infinite',
          }}
        />
        <div
          className="line absolute h-0.5 bg-neon-blue opacity-40"
          style={{
            width: '200px',
            right: 0,
            top: '28px',
            boxShadow: '0 0 10px var(--neon-blue)',
            animation: 'pulse 2.2s ease-in-out infinite',
          }}
        />
      </div>

      {/* Lower left lines */}
      <div ref={lowerRef} className="fixed bottom-20 left-0 z-0">
        <div
          className="line absolute h-0.5 bg-neon-blue opacity-40"
          style={{
            width: '200px',
            left: 0,
            bottom: '28px',
            boxShadow: '0 0 10px var(--neon-blue)',
            animation: 'pulse 2.4s ease-in-out infinite',
          }}
        />
        <div
          className="line absolute h-0.5 bg-neon-blue opacity-40"
          style={{
            width: '300px',
            left: 0,
            bottom: '20px',
            boxShadow: '0 0 10px var(--neon-blue)',
            animation: 'pulse 1.6s ease-in-out infinite',
          }}
        />
      </div>
    </>
  )
}
