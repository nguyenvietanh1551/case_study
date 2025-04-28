// Các phần tử trong trò chơi
const gameContainer = document.getElementById('gameContainer');
const ball = document.getElementById('ball');
const bar = document.getElementById('bar');
const scoreDisplay = document.getElementById('scoreDisplay');
const gameOverDisplay = document.getElementById('gameOver');
const restartBtn = document.getElementById('restartBtn');

// Các hằng số trong trò chơi
const GAME_WIDTH = 600;
const GAME_HEIGHT = 400;
const BALL_SIZE = 20;
const BAR_WIDTH = 100;
const BAR_HEIGHT = 15;
const INITIAL_BALL_SPEED = 3;
const BAR_SPEED = 8;

// Trạng thái trò chơi
let balls = [];
let barX = (GAME_WIDTH - BAR_WIDTH) / 2;
let score = 0;
let milestone = 100; // Thêm bóng mới mỗi khi đạt mốc này
let gameRunning = true;
let animationId;
let lastTimestamp = 0;

// Phím
let leftPressed = false;
let rightPressed = false;

// Âm thanh
const hitSound = new Audio('ting.mp3');
const gameOverSound = new Audio('gameover.mp3');

// Khởi tạo trò chơi
function initGame() {
    balls = [createBall()]; // tạo 1 bóng
    barX = (GAME_WIDTH - BAR_WIDTH) / 2;
    score = 0;
    milestone = 100;
    gameRunning = true;

    gameOverDisplay.style.display = 'none';
    restartBtn.style.display = 'none';
    scoreDisplay.textContent = 'Score: 0';

    updatePositions();
    lastTimestamp = performance.now();
    animationId = requestAnimationFrame(gameLoop);
}

function createBall() {
    const angle = Math.random() * Math.PI/3 + Math.PI/6;
    let speed = INITIAL_BALL_SPEED;
    let dirX = speed * Math.cos(angle);
    let dirY = speed * Math.sin(angle);
    if (Math.random() > 0.5) dirX *= -1;

    // Tạo quả bóng mới (clone từ ball gốc)
    const newBall = ball.cloneNode(true);
    gameContainer.appendChild(newBall);

    // thêm màu khi đạt điểm + bóng mới
    const randomColor = `hsl(${Math.floor(Math.random() * 360)}, 80%, 50%)`;
    newBall.style.backgroundColor = randomColor;

    return {
        x: GAME_WIDTH / 2,
        y: GAME_HEIGHT / 2,
        speedX: dirX,
        speedY: dirY,
        element: newBall
    };
}

// Cập nhật vị trí của quả bóng và thanh bar
function updatePositions() {
    balls.forEach(b => {
        b.element.style.left = `${b.x}px`;
        b.element.style.top = `${b.y}px`;
    });
    bar.style.left = `${barX}px`;
}

// Vòng lặp trò chơi
function gameLoop(timestamp) {
    if (!gameRunning) return;

    const deltaTime = timestamp - lastTimestamp;
    lastTimestamp = timestamp;

    // Di chuyển thanh bar
    if (leftPressed) {
        barX = Math.max(0, barX - BAR_SPEED * (deltaTime / 16));
    }
    if (rightPressed) {
        barX = Math.min(GAME_WIDTH - BAR_WIDTH, barX + BAR_SPEED * (deltaTime / 16));
    }

    balls.forEach((b, index) => {
        b.x += b.speedX * (deltaTime / 16);
        b.y += b.speedY * (deltaTime / 16);

        // Va chạm tường
        if (b.x <= 0) {
            b.x = 0;
            b.speedX *= -1;
        }
        if (b.x + BALL_SIZE >= GAME_WIDTH) {
            b.x = GAME_WIDTH - BALL_SIZE;
            b.speedX *= -1;
        }
        if (b.y <= 0) {
            b.y = 0;
            b.speedY *= -1;
        }
        if (b.y + BALL_SIZE >= GAME_HEIGHT) {
            // Bóng rơi khỏi màn → xóa bóng đó
            gameContainer.removeChild(b.element);
            balls.splice(index, 1);

            if (balls.length === 0) {
                gameOver();
            }
        }

        // Va chạm với thanh bar
        if (b.y + BALL_SIZE >= GAME_HEIGHT - 20 &&
            b.x + BALL_SIZE >= barX &&
            b.x <= barX + BAR_WIDTH) {

            const hitPosition = (b.x + BALL_SIZE / 2 - barX) / BAR_WIDTH;
            const angle = (hitPosition - 0.5) * Math.PI / 2;
            const speed = Math.sqrt(b.speedX ** 2 + b.speedY ** 2) * 1.02;

            b.speedX = speed * Math.sin(angle);
            b.speedY = -speed * Math.cos(angle);
            b.y = GAME_HEIGHT - 20 - BALL_SIZE;

            // Cộng điểm
            score += 10;
            scoreDisplay.textContent = `Score: ${score}`;

            // Phát nhạc
            hitSound.currentTime = 0;
            hitSound.play();

            // Thêm bóng mới nếu đạt mốc điểm
            if (score >= milestone) {
                balls.push(createBall());
                milestone += 100;
            }
        }
    });

    updatePositions();
    animationId = requestAnimationFrame(gameLoop);
}

// Xử lý phím
document.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowLeft' || e.key === 'Left') {
        leftPressed = true;
    } else if (e.key === 'ArrowRight' || e.key === 'Right') {
        rightPressed = true;
    } else if (!gameRunning && (e.code === 'Space' || e.key === ' ')) {
        initGame();
    }
});

document.addEventListener('keyup', (e) => {
    if (e.key === 'ArrowLeft' || e.key === 'Left') {
        leftPressed = false;
    } else if (e.key === 'ArrowRight' || e.key === 'Right') {
        rightPressed = false;
    }
});

// Kết thúc trò chơi
function gameOver() {
    gameRunning = false;
    cancelAnimationFrame(animationId);

    // Phát nhạc gameover
    gameOverSound.currentTime = 0;
    gameOverSound.play();

    gameOverDisplay.style.display = 'block';
    restartBtn.style.display = 'block';
}

// Khởi động lại game
restartBtn.addEventListener('click', initGame);

// Khởi động ban đầu
initGame();
