const scrollElement = document.getElementById('scroll');
let defaultZoom = 1;


function updateScrollHeight() {
    const currentZoom = document.documentElement.clientWidth / window.innerWidth;
    const zoomDifference = currentZoom - defaultZoom;
    const newHeightPercentage = 48.4 * (1 + zoomDifference);

    scrollElement.style.height = `${newHeightPercentage}%`;
}


window.addEventListener('load', () => {
    defaultZoom = document.documentElement.clientWidth / window.innerWidth;
});

window.addEventListener('wheel', updateScrollHeight);
window.addEventListener('scroll', updateScrollHeight);