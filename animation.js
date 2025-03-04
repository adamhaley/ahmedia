gsap.registerPlugin(ScrollTrigger);

// Initial animation
gsap.from('.text-section h1', {
    duration: 1.5,
    opacity: 0,
    y: 100,
    ease: 'power4.out'
});

// Animate circles on load
gsap.from('.circle', {
    duration: 1.5,
    scale: 0.4,
    opacity: 0,
    stagger: 0.2,
    ease: 'power4.out'
});

// Scroll-based animations
gsap.to('.circle-1', {
    scrollTrigger: {
        trigger: '.splash-container',
        start: 'top top',
        end: 'bottom top',
        scrub: 1
    },
    scale: 0.8,
    rotation: 180
});

gsap.to('.circle-2', {
    scrollTrigger: {
        trigger: '.splash-container',
        start: 'top top',
        end: 'bottom top',
        scrub: 1
    },
    scale: 0.8,
    rotation: -180
});

gsap.to('.circle-3', {
    scrollTrigger: {
        trigger: '.splash-container',
        start: 'top top',
        end: 'bottom top',
        scrub: 1
    },
    scale: 0.8,
    rotation: 360
});

// Line animations
gsap.to('.line-1', {
    scrollTrigger: {
        trigger: '.splash-container',
        start: 'top top',
        end: 'bottom top',
        scrub: 1
    },
    scaleX: 0,
    transformOrigin: 'right',
    opacity: 0.2
});

gsap.to('.line-2', {
    scrollTrigger: {
        trigger: '.splash-container',
        start: 'top top',
        end: 'bottom top',
        scrub: 1.5
    },
    scaleX: 0,
    transformOrigin: 'right',
    opacity: 0.2
});

gsap.to('.line-3', {
    scrollTrigger: {
        trigger: '.splash-container',
        start: 'top top',
        end: 'bottom top',
        scrub: 1
    },
    scaleX: 0,
    transformOrigin: 'left',
    opacity: 0.2
});

gsap.to('.line-4', {
    scrollTrigger: {
        trigger: '.splash-container',
        start: 'top top',
        end: 'bottom top',
        scrub: 1.5
    },
    scaleX: 0,
    transformOrigin: 'left',
    opacity: 0.2
}); 