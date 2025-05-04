// Các phần tử
const gameContainer = document.getElementById('gameContainer');
const ball = document.getElementById('ball');
const bar = document.getElementById('bar');
const scoreDisplay = document.getElementById('scoreDisplay');
const gameOverDisplay = document.getElementById('gameOver');
const restartBtn = document.getElementById('restartBtn');
const inputNameContainer = document.getElementById('inputNameContainer');
const playerNameInput = document.getElementById('playerNameInput');
const startGameBtn = document.getElementById('startGameBtn');
const mainContent = document.getElementById('mainContent');
const highScoreList = document.getElementById('highScoreList');

// Game config
const GAME_WIDTH = 600;
const GAME_HEIGHT = 400;
const BALL_SIZE = 20;
const BAR_WIDTH = 100;
const BAR_HEIGHT = 15;
const INITIAL_BALL_SPEED = 5;
const BAR_SPEED = 8;

// Game state
let balls = [];
let barX = (GAME_WIDTH - BAR_WIDTH) / 2;
let score = 0;
let highScore = localStorage.getItem('highScore') || 0;
let playerName = '';
let milestone = 100;
let gameRunning = false;
let animationId;
let lastTimestamp = 0;

// Controls
let leftPressed = false;
let rightPressed = false;

// Sounds
const hitSound = new Audio('ting.mp3');
const gameOverSound = new Audio('gameover.mp3');

// Start game
startGameBtn.addEventListener('click', () => {
    const name = playerNameInput.value.trim();
    if (name) {
        playerName = name;
        inputNameContainer.style.display = 'none';
        mainContent.style.display = 'flex';
        initGame();
        updateLeaderboard();
    } else {
        alert('Please enter your name!');
    }
});

function initGame() {
    balls = [createBall()];
    barX = (GAME_WIDTH - BAR_WIDTH) / 2;
    score = 0;
    milestone = 100;
    gameRunning = true;

    gameOverDisplay.style.display = 'none';
    gameOverDisplay.textContent = 'Game Over!';
    restartBtn.style.display = 'none';
    scoreDisplay.textContent = `Score: 0 | High Score: ${highScore}`;

    updatePositions();
    lastTimestamp = performance.now();
    animationId = requestAnimationFrame(gameLoop);
}

function createBall() {
    const angle = Math.random() * Math.PI / 3 + Math.PI / 6;
    let speed = INITIAL_BALL_SPEED;
    let dirX = speed * Math.cos(angle);
    let dirY = speed * Math.sin(angle);
    if (Math.random() > 0.5) dirX *= -1;

    const newBall = ball.cloneNode(true);
    gameContainer.appendChild(newBall);
    newBall.style.backgroundColor = `hsl(${Math.floor(Math.random() * 360)}, 80%, 50%)`;

    return {
        x: GAME_WIDTH / 2,
        y: GAME_HEIGHT / 2,
        speedX: dirX,
        speedY: dirY,
        element: newBall
    };
}

function updatePositions() {
    balls.forEach(b => {
        b.element.style.left = `${b.x}px`;
        b.element.style.top = `${b.y}px`;
    });
    bar.style.left = `${barX}px`;
}

function gameLoop(timestamp) {
    if (!gameRunning) return;

    const deltaTime = timestamp - lastTimestamp;
    lastTimestamp = timestamp;

    if (leftPressed) {
        barX = Math.max(0, barX - BAR_SPEED * (deltaTime / 16));
    }
    if (rightPressed) {
        barX = Math.min(GAME_WIDTH - BAR_WIDTH, barX + BAR_SPEED * (deltaTime / 16));
    }

    balls.forEach((b, index) => {
        b.x += b.speedX * (deltaTime / 16);
        b.y += b.speedY * (deltaTime / 16);

        if (b.x <= 0 || b.x + BALL_SIZE >= GAME_WIDTH) {
            b.speedX *= -1;
            b.x = Math.max(0, Math.min(b.x, GAME_WIDTH - BALL_SIZE));
        }

        if (b.y <= 0) {
            b.speedY *= -1;
            b.y = 0;
        }

        if (b.y + BALL_SIZE >= GAME_HEIGHT) {
            gameContainer.removeChild(b.element);
            balls.splice(index, 1);
            if (balls.length === 0) gameOver();
        }

        if (b.y + BALL_SIZE >= GAME_HEIGHT - 20 &&
            b.x + BALL_SIZE >= barX &&
            b.x <= barX + BAR_WIDTH) {

            const hitPosition = (b.x + BALL_SIZE / 2 - barX) / BAR_WIDTH;
            const angle = (hitPosition - 0.5) * Math.PI / 2;
            const speed = Math.sqrt(b.speedX ** 2 + b.speedY ** 2) * 1.02;

            b.speedX = speed * Math.sin(angle);
            b.speedY = -speed * Math.cos(angle);
            b.y = GAME_HEIGHT - 20 - BALL_SIZE;

            score += 10;
            scoreDisplay.textContent = `Score: ${score} | High Score: ${highScore}`;
            hitSound.currentTime = 0;
            hitSound.play();

            if (score >= milestone) {
                balls.push(createBall());
                milestone += 100;
            }
        }
    });

    updatePositions();
    animationId = requestAnimationFrame(gameLoop);
}

function gameOver() {
    gameRunning = false;
    cancelAnimationFrame(animationId);

    if (score > highScore) {
        highScore = score;
        localStorage.setItem('highScore', highScore);
        gameOverDisplay.textContent = '🎉 New High Score!';
    }

    saveScore();
    updateLeaderboard();

    scoreDisplay.textContent = `Score: ${score} | High Score: ${highScore}`;
    gameOverSound.currentTime = 0;
    gameOverSound.play();
    gameOverDisplay.style.display = 'block';
    restartBtn.style.display = 'block';
}

restartBtn.addEventListener('click', initGame);

document.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowLeft' || e.key === 'Left') leftPressed = true;
    else if (e.key === 'ArrowRight' || e.key === 'Right') rightPressed = true;
});

document.addEventListener('keyup', (e) => {
    if (e.key === 'ArrowLeft' || e.key === 'Left') leftPressed = false;
    else if (e.key === 'ArrowRight' || e.key === 'Right') rightPressed = false;
});

function saveScore() {
    const scores = JSON.parse(localStorage.getItem('playerScores')) || [];
    scores.push({ name: playerName, score });
    scores.sort((a, b) => b.score - a.score);
    localStorage.setItem('playerScores', JSON.stringify(scores.slice(0, 3)));
}

function updateLeaderboard() {
    const scores = JSON.parse(localStorage.getItem('playerScores')) || [];
    highScoreList.innerHTML = '';
    scores.forEach((s, i) => {
        const li = document.createElement('li');
        li.textContent = `${i + 1}. ${s.name} - ${s.score}`;
        highScoreList.appendChild(li);
    });
}
