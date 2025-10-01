// Scroll To Top
window.onscroll = function () {
    scrollFunction();
};

document.addEventListener("DOMContentLoaded", function () {
    const scrollToTopBtn = document.getElementById("scrollToTopBtn");

    if (scrollToTopBtn) {
        scrollToTopBtn.addEventListener("click", function () {
            document.body.scrollTop = 0; // Safari
            document.documentElement.scrollTop = 0; // Chrome, Firefox, IE, Opera
        });
    }
});

function scrollFunction() {
    const btn = document.getElementById("scrollToTopBtn");
    if (!btn) return;

    if (
        document.body.scrollTop > 20 ||
        document.documentElement.scrollTop > 20
    ) {
        btn.style.display = "block";
    } else {
        btn.style.display = "none";
    }
}
