const canvas = document.getElementById("drawingBoard");
const ctx = canvas.getContext("2d");

let isPainting = false;
let currentColor = "#c0392b";
let history = [];

// Fill white so saved image has a background
ctx.fillStyle = "#ffffff";
ctx.fillRect(0, 0, canvas.width, canvas.height);
saveState();

// ── Unified position getter (handles mouse + touch + scaling) ──
function getPos(e) {
    const rect = canvas.getBoundingClientRect();
    const scaleX = canvas.width / rect.width;
    const scaleY = canvas.height / rect.height;
    const clientX = e.touches ? e.touches[0].clientX : e.clientX;
    const clientY = e.touches ? e.touches[0].clientY : e.clientY;
    return {
        x: (clientX - rect.left) * scaleX,
        y: (clientY - rect.top) * scaleY
    };
}

function startPainting(e) {
    e.preventDefault();
    isPainting = true;
    const pos = getPos(e);
    ctx.beginPath();
    ctx.moveTo(pos.x, pos.y);
}

function stopPainting(e) {
    if (!isPainting) return;
    isPainting = false;
    ctx.beginPath();
    saveState();
}

function draw(e) {
    e.preventDefault();
    if (!isPainting) return;
    const pos = getPos(e);
    ctx.lineWidth = 5;
    ctx.lineCap = "round";
    ctx.lineJoin = "round";
    ctx.strokeStyle = currentColor;
    ctx.lineTo(pos.x, pos.y);
    ctx.stroke();
}

// ── Events ──
canvas.addEventListener("mousedown", startPainting);
canvas.addEventListener("mouseup", stopPainting);
canvas.addEventListener("mouseleave", stopPainting);
canvas.addEventListener("mousemove", draw);
canvas.addEventListener("touchstart", startPainting, { passive: false });
canvas.addEventListener("touchmove", draw, { passive: false });
canvas.addEventListener("touchend", stopPainting, { passive: false });

// ── Undo ──
function saveState() {
    history.push(ctx.getImageData(0, 0, canvas.width, canvas.height));
}

document.getElementById("undoBtn").addEventListener("click", function () {
    if (history.length > 1) {
        history.pop();
        ctx.putImageData(history[history.length - 1], 0, 0);
    } else {
        history = [];
        ctx.fillStyle = "#ffffff";
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        saveState();
    }
});

// ── Color palette ──
document.querySelectorAll(".color-btn").forEach(btn => {
    btn.addEventListener("click", function () {
        document.querySelectorAll(".color-btn").forEach(b => b.classList.remove("active"));
        this.classList.add("active");
        currentColor = this.getAttribute("data-color");
    });
});

// ── Submit ──
document.getElementById("submitDoodleBtn").addEventListener("click", function () {
    const dataURL = canvas.toDataURL("image/png");
    document.getElementById("doodle_image").value = dataURL;
    document.getElementById("doodleForm").submit();
});