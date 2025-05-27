document.addEventListener('DOMContentLoaded', () => {
    const newsCards = document.querySelectorAll('.news-card, .most-popular-news');

    newsCards.forEach(card => {
        card.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                const link = card.querySelector('.news-button, .prominent-button');
                if (link) {
                    link.click();
                }
            }
        });

        card.setAttribute('tabindex', '0'); 
    });
});