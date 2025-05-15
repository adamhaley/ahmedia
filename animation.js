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

    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('file-input');
    const uploadStatus = document.getElementById('upload-status');

    // Prevent default drag behaviors
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });

    // Highlight drop zone when dragging over it
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });

    // Handle dropped files
    dropZone.addEventListener('drop', handleDrop, false);
    
    // Handle file input change
    fileInput.addEventListener('change', (e) => {
        handleFiles(e.target.files);
    });

    function preventDefaults (e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function highlight(e) {
        dropZone.classList.add('dragover');
    }

    function unhighlight(e) {
        dropZone.classList.remove('dragover');
    }

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles(files);
    }

    function handleFiles(files) {
        uploadStatus.innerHTML = '';
        ([...files]).forEach(uploadFile);
    }

    async function uploadFile(file) {
        const formData = new FormData();
        formData.append('file', file);

        const status = document.createElement('p');
        status.textContent = `Uploading ${file.name}...`;
        uploadStatus.appendChild(status);


        let postUrl = 'https://n8n.ahmedia.ai/webhook/fded596d-4a61-4fd2-90a4-006df43136bf';

        try {
            const response = await fetch(postUrl, {
                method: 'POST',
                body: formData
            });

            if (response.ok) {
                const data = await response.json();

                //set localstorage key namespace to data.namespace
                localStorage.setItem('namespace', data.namespace);
                console.log('Data received:', data);
                status.textContent = `${file.name} uploaded successfully!`;
                status.style.color = '#00ff00';
            } else {
                throw new Error('Upload failed');
            }
        } catch (error) {
            status.textContent = `Failed to upload ${file.name}`;
            status.style.color = '#ff0000';
            console.error('Error:', error);
        }
    }
}); 
