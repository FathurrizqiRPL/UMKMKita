<style>
.back-to-top {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 50px;
    height: 50px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease, visibility 0.3s ease, transform 0.2s ease;
    z-index: 1000;
}

.back-to-top.show {
    opacity: 1;
    visibility: visible;
}

.back-to-top:hover {
    transform: translateY(-3px);
}

.progress-ring {
    position: absolute;
    top: 0;
    left: 0;
    transform: rotate(-90deg);
}

.progress-ring__bg {
    fill: transparent;
    stroke: #e6e6e6;
    stroke-width: 3;
}

.progress-ring__circle {
    fill: transparent;
    stroke: #d90429;
    stroke-width: 3;
    stroke-linecap: round;
    transition: stroke-dashoffset 0.1s linear;
}

.back-to-top .arrow {
    font-size: 16px;
    font-weight: bold;
    color: #e65c5c;
    transform: rotate(90deg);
    user-select: none;
}
</style>

<div id="backToTop" class="back-to-top">
    <svg class="progress-ring" width="50" height="50" viewBox="0 0 50 50">
        <circle class="progress-ring__bg" cx="25" cy="25" r="20"></circle>
        <circle class="progress-ring__circle" cx="25" cy="25" r="20"></circle>
    </svg>
    <span class="arrow">&#10094;</span>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const backToTopBtn = document.getElementById('backToTop');
    const circle = document.querySelector('.progress-ring__circle');
    
    if (!backToTopBtn || !circle) return;

    const radius = circle.r.baseVal.value;
    const circumference = 2 * Math.PI * radius;

    circle.style.strokeDasharray = `${circumference} ${circumference}`;
    circle.style.strokeDashoffset = circumference;

    function updateProgress() {
        const scrollTop = window.scrollY || document.documentElement.scrollTop;
        const scrollHeight = document.documentElement.scrollHeight - window.innerHeight;
        const scrollFraction = scrollHeight > 0 ? scrollTop / scrollHeight : 0;
        const offset = circumference - (scrollFraction * circumference);
        
        circle.style.strokeDashoffset = offset;

        if (scrollTop > 100) {
            backToTopBtn.classList.add('show');
        } else {
            backToTopBtn.classList.remove('show');
        }
    }

    window.addEventListener('scroll', updateProgress);

    backToTopBtn.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
});
</script>