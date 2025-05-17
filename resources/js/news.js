document.addEventListener("DOMContentLoaded", () => {
    const cards = document.querySelectorAll(".news-card");

    cards.forEach(card => {
        card.style.transition = "transform 0.4s ease, box-shadow 0.4s ease";

        card.addEventListener("mouseenter", () => {
            card.style.transform = "scale(1.2)";
            card.style.zIndex = "1000";
            card.style.boxShadow = "0 12px 25px rgba(0, 0, 0, 0.2)";
        });

        card.addEventListener("mouseleave", () => {
            card.style.transform = "scale(1)";
            card.style.zIndex = "1";
            card.style.boxShadow = "0 8px 20px rgba(0, 0, 0, 0.1)";
        });
    });
});
