document.addEventListener('DOMContentLoaded', () => {
    const horizontalCarousels = document.querySelectorAll('.horizontal-carousel-slide');

    horizontalCarousels.forEach(carousel => {
        const original = carousel.innerHTML;
        carousel.innerHTML += original; 

        carousel.addEventListener('mouseenter', () => {
            carousel.style.animationPlayState = 'paused';
            const hoveredCard = event.target.closest('.horizontal-card');
            if (hoveredCard) {
                hoveredCard.style.transform = 'scale(1.08)';
                hoveredCard.style.boxShadow = '0 12px 20px rgba(0,0,0,0.25)';
            }
        });

        carousel.addEventListener('mouseleave', () => {
            carousel.style.animationPlayState = 'running';
            const hoveredCard = event.target.closest('.horizontal-card');
            if (hoveredCard) {
                hoveredCard.style.transform = 'scale(1)';
                hoveredCard.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.1)';
            }
        });

        carousel.querySelectorAll('.horizontal-card').forEach(card => {
            card.addEventListener('mouseenter', () => {
                horizontalCarousels.forEach(otherCarousel => {
                    if (otherCarousel !== carousel) {
                        otherCarousel.style.animationPlayState = 'paused';
                    }
                });
                card.style.transform = 'scale(1.08)';
                card.style.boxShadow = '0 12px 20px rgba(0,0,0,0.25)';
            });

            card.addEventListener('mouseleave', () => {
                horizontalCarousels.forEach(otherCarousel => {
                    otherCarousel.style.animationPlayState = 'running';
                });
                card.style.transform = 'scale(1)';
                card.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.1)';
            });
        });
    });

    document.querySelectorAll('.news-card').forEach(card => {
        card.addEventListener('click', (event) => {
            const articleLink = card.querySelector('.news-link');

            if (articleLink) {
                window.open(articleLink.href, '_blank'); 
            }
        });
    });
});