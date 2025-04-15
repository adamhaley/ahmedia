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
        scrub: 1,
        onUpdate: (self) => {
            const scale = 0.4 + (self.progress * 0.4); // Scale from 0.4 to 0.8
            document.querySelector('.circle-1').style.setProperty('--base-scale', scale);
        }
    },
    rotation: 180
});

gsap.to('.circle-2', {
    scrollTrigger: {
        trigger: '.splash-container',
        start: 'top top',
        end: 'bottom top',
        scrub: 1,
        onUpdate: (self) => {
            const scale = 0.4 + (self.progress * 0.4);
            document.querySelector('.circle-2').style.setProperty('--base-scale', scale);
        }
    },
    rotation: -180
});

gsap.to('.circle-3', {
    scrollTrigger: {
        trigger: '.splash-container',
        start: 'top top',
        end: 'bottom top',
        scrub: 1,
        onUpdate: (self) => {
            const scale = 0.4 + (self.progress * 0.4);
            document.querySelector('.circle-3').style.setProperty('--base-scale', scale);
        }
    },
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

document.addEventListener('DOMContentLoaded', () => {
    // Wait a bit for initial page render
    setTimeout(() => {
        const cards = document.querySelectorAll('.service-card');
        let delay = 0;

        cards.forEach((card, index) => {
            // Trigger hover effect
            setTimeout(() => {
                card.classList.add('animate-hover');
                
                // Remove the class after animation
                setTimeout(() => {
                    card.classList.remove('animate-hover');
                }, 200);
            }, delay);
            
            delay += 240;
        });
    }, 100);
});

document.getElementById('email-form')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    const email = form.querySelector('input[type="email"]').value;
    const button = form.querySelector('button');
    
    // Disable the button during submission
    button.disabled = true;
    button.textContent = 'Sending...';
    
    try {
        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ email }),
        });
        
        if (response.ok) {
            // Clear the input
            form.querySelector('input[type="email"]').value = '';
            
            // Show success message
            button.textContent = 'Success! ✓';
            button.style.backgroundColor = '#00ff00';
            
            // Reset button after 3 seconds
            setTimeout(() => {
                button.textContent = 'Keep Me Updated!';
                button.style.backgroundColor = '';
                button.disabled = false;
            }, 3000);
        } else {
            throw new Error('Submission failed');
        }
    } catch (error) {
        console.error('Error:', error);
        button.textContent = 'Error - Try Again';
        button.style.backgroundColor = '#ff0000';
        
        // Reset button after 3 seconds
        setTimeout(() => {
            button.textContent = 'Keep Me Updated!';
            button.style.backgroundColor = '';
            button.disabled = false;
        }, 3000);
    }
}); 
