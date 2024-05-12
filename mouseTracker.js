const position = {x : 0, y : 0};

const getCursorPosition = function(x,y) {
    position.x = (x / window.innerWidth).toFixed(2);
    position.y = (y / window.innerHeight).toFixed(2);
    document.documentElement.style.setProperty('--x', position.x);
    document.documentElement.style.setProperty('--y', position.y);
}
document,addEventListener('mousemove', f => {
    getCursorPosition(f.clientX, f.clientY);
})