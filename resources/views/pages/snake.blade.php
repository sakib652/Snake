@extends('layouts.app')

@section('title', 'Snake Game')

@section('content')
    <div id="scoreContainer">
        <h2>Score</h2>
        <div id="score">0</div>
    </div>

    <canvas id="snakeGame" width="400" height="400"></canvas>

    <div id="buttons">
        <button id="resetButton">Reset Game</button>
        <button id="exitButton">Exit</button>
    </div>
@endsection

@push('scripts')
    <script>
        const canvas = document.getElementById('snakeGame');
        const ctx = canvas.getContext('2d');
        const resetButton = document.getElementById('resetButton');
        const exitButton = document.getElementById('exitButton');
        const scoreDisplay = document.getElementById('score');

        const box = 20;
        let snake;
        let food;
        let score;
        let direction;
        let game;

        let reverseSnake = [];
        let isReverseMode = false;
        let reverseDirection = null;
        let reverseTimer = null;
        let reverseDuration = 20000; // 20 seconds (in milliseconds)

        document.addEventListener('keydown', directionControl);
        resetButton.addEventListener('click', resetGame);
        exitButton.addEventListener('click', exitGame);

        function initGame() {
            snake = [{
                x: 9 * box,
                y: 10 * box
            }];
            food = {
                x: Math.floor(Math.random() * 19 + 1) * box,
                y: Math.floor(Math.random() * 19 + 1) * box
            };
            score = 0;
            direction = null;

            resetButton.style.display = 'none';
            exitButton.style.display = 'none';
            updateScoreDisplay();
            clearInterval(game);
            game = setInterval(draw, 100);

            isReverseMode = false;
            clearInterval(reverseTimer);
        }

        function directionControl(event) {
            if (event.keyCode == 37 && direction != "RIGHT") {
                direction = "LEFT";
            } else if (event.keyCode == 38 && direction != "DOWN") {
                direction = "UP";
            } else if (event.keyCode == 39 && direction != "LEFT") {
                direction = "RIGHT";
            } else if (event.keyCode == 40 && direction != "UP") {
                direction = "DOWN";
            }
        }

        function collision(newHead, snake) {
            for (let i = 0; i < snake.length; i++) {
                if (newHead.x == snake[i].x && newHead.y == snake[i].y) {
                    return true;
                }
            }
            return false;
        }

        function draw() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Draw Snake
            for (let i = 0; i < snake.length; i++) {
                if (i === 0) {
                    drawSnakeHead(snake[i].x, snake[i].y);
                } else {
                    drawSnakeBody(snake[i].x, snake[i].y, i);
                }
            }

            // Draw Food
            ctx.fillStyle = "#006400";
            ctx.beginPath();
            ctx.arc(food.x + box / 2, food.y + box / 2, box / 2, 0, Math.PI * 2);
            ctx.fill();

            let snakeX = snake[0].x;
            let snakeY = snake[0].y;

            // Move the snake
            if (direction == "LEFT") snakeX -= box;
            if (direction == "UP") snakeY -= box;
            if (direction == "RIGHT") snakeX += box;
            if (direction == "DOWN") snakeY += box;

            // Check if snake eats the food
            if (snakeX == food.x && snakeY == food.y) {
                score++;
                updateScoreDisplay();
                food = {
                    x: Math.floor(Math.random() * 19 + 1) * box,
                    y: Math.floor(Math.random() * 19 + 1) * box
                };

                // Check if reverse snake should appear
                if (score % 10 === 0 && score !== 0) {
                    startReverseSnake();
                }
            } else {
                snake.pop();
            }

            let newHead = {
                x: snakeX,
                y: snakeY
            };

            // Check for collisions
            if (snakeX < 0 || snakeX >= canvas.width || snakeY < 0 || snakeY >= canvas.height || collision(newHead,
                    snake)) {
                clearInterval(game);
                clearInterval(reverseTimer); // Stop reverse snake if game over
                submitScore(score);
                showResetButton();
            } else {
                snake.unshift(newHead);
            }

            // Handle reverse snake mode
            if (isReverseMode) {
                moveReverseSnake();
                drawReverseSnake();
                if (collision(newHead, reverseSnake)) {
                    clearInterval(game);
                    clearInterval(reverseTimer);
                    submitScore(score);
                    showResetButton();
                }
            }
        }

        // Function to draw a playful snake head
        function drawSnakeHead(x, y) {
            ctx.fillStyle = "#FF4500";
            ctx.beginPath();
            ctx.arc(x + box / 2, y + box / 2, box / 2 + 5, 0, Math.PI * 2);
            ctx.fill();

            // Draw the eyes
            ctx.fillStyle = "white";
            ctx.beginPath();
            if (direction === "UP") {
                // Vertical alignment for eyes
                ctx.arc(x + box / 2 - 5, y + box / 2 - 15, 3, 0, Math.PI * 2); // Left eye
                ctx.arc(x + box / 2 + 5, y + box / 2 - 15, 3, 0, Math.PI * 2); // Right eye
            } else if (direction === "DOWN") {
                ctx.arc(x + box / 2 - 5, y + box / 2 + 15, 3, 0, Math.PI * 2); // Left eye
                ctx.arc(x + box / 2 + 5, y + box / 2 + 15, 3, 0, Math.PI * 2); // Right eye
            } else if (direction === "LEFT") {
                ctx.arc(x + box / 2 - 15, y + box / 2 - 5, 3, 0, Math.PI * 2); // Left eye
                ctx.arc(x + box / 2 - 15, y + box / 2 + 5, 3, 0, Math.PI * 2); // Right eye
            } else if (direction === "RIGHT") {
                ctx.arc(x + box / 2 + 15, y + box / 2 - 5, 3, 0, Math.PI * 2); // Left eye
                ctx.arc(x + box / 2 + 15, y + box / 2 + 5, 3, 0, Math.PI * 2); // Right eye
            }
            ctx.fill();

            // Draw the pupils
            ctx.fillStyle = "black";
            ctx.beginPath();
            if (direction === "UP") {
                ctx.arc(x + box / 2 - 5, y + box / 2 - 15, 1.5, 0, Math.PI * 2); // Left pupil
                ctx.arc(x + box / 2 + 5, y + box / 2 - 15, 1.5, 0, Math.PI * 2); // Right pupil
            } else if (direction === "DOWN") {
                ctx.arc(x + box / 2 - 5, y + box / 2 + 15, 1.5, 0, Math.PI * 2); // Left pupil
                ctx.arc(x + box / 2 + 5, y + box / 2 + 15, 1.5, 0, Math.PI * 2); // Right pupil
            } else if (direction === "LEFT") {
                ctx.arc(x + box / 2 - 15, y + box / 2 - 5, 1.5, 0, Math.PI * 2); // Left pupil
                ctx.arc(x + box / 2 - 15, y + box / 2 + 5, 1.5, 0, Math.PI * 2); // Right pupil
            } else if (direction === "RIGHT") {
                ctx.arc(x + box / 2 + 15, y + box / 2 - 5, 1.5, 0, Math.PI * 2); // Left pupil
                ctx.arc(x + box / 2 + 15, y + box / 2 + 5, 1.5, 0, Math.PI * 2); // Right pupil
            }
            ctx.fill();

            // Draw the tongue
            ctx.strokeStyle = "#FF6347";
            ctx.beginPath();

            if (direction === "UP") {
                ctx.moveTo(x + box / 2, y + box / 2 - box / 2 - 10); // Tongue start above the head
                ctx.lineTo(x + box / 2, y + box / 2 - box / 2 - 20); // Draw tongue upward
            } else if (direction === "DOWN") {
                ctx.moveTo(x + box / 2, y + box / 2 + box / 2); // Tongue start below the head
                ctx.lineTo(x + box / 2, y + box / 2 + box / 2 + 10); // Draw tongue downward
            } else if (direction === "LEFT") {
                ctx.moveTo(x + box / 2 - box / 2 - 10, y + box / 2); // Tongue start left of the head
                ctx.lineTo(x + box / 2 - box / 2 - 20, y + box / 2); // Draw tongue leftward
            } else if (direction === "RIGHT") {
                ctx.moveTo(x + box / 2 + box / 2, y + box / 2); // Tongue start right of the head
                ctx.lineTo(x + box / 2 + box / 2 + 10, y + box / 2); // Draw tongue rightward
            }

            ctx.stroke();
        }

        // Function to draw a snake body segment
        function drawSnakeBody(x, y, i) {
            ctx.fillStyle = i % 2 === 0 ? "#FF4500" : "#FFD966";
            ctx.beginPath();
            ctx.arc(x + box / 2, y + box / 2, box / 2, 0, Math.PI * 2);
            ctx.fill();
        }

        // Reverse Snake Logic
        function startReverseSnake() {
            reverseSnake = []; // Initialize an empty reverse snake
            let initialX = Math.floor(Math.random() * 19 + 1) * box;
            let initialY = Math.floor(Math.random() * 19 + 1) * box;

            // Set default length for reverse snake
            let defaultReverseSnakeLength = 5;

            // Initialize the reverse snake with the default length
            for (let i = 0; i < defaultReverseSnakeLength; i++) {
                reverseSnake.push({
                    x: initialX,
                    y: initialY
                });
            }

            isReverseMode = true;
            reverseDirection = getRandomDirection();

            reverseTimer = setTimeout(() => {
                isReverseMode = false;
                reverseSnake = [];
            }, reverseDuration);
        }

        function getRandomDirection() {
            const directions = ["LEFT", "UP", "RIGHT", "DOWN"];
            return directions[Math.floor(Math.random() * directions.length)];
        }

        function moveReverseSnake() {
            let reverseX = reverseSnake[0].x;
            let reverseY = reverseSnake[0].y;

            if (reverseDirection == "LEFT") reverseX -= box;
            if (reverseDirection == "UP") reverseY -= box;
            if (reverseDirection == "RIGHT") reverseX += box;
            if (reverseDirection == "DOWN") reverseY += box;

            if (reverseX < 0 || reverseX >= canvas.width || reverseY < 0 || reverseY >= canvas.height) {
                reverseDirection = getRandomDirection(); // Change direction if about to hit the wall
            }

            let newReverseHead = {
                x: reverseX,
                y: reverseY
            };

            reverseSnake.pop();
            reverseSnake.unshift(newReverseHead);
        }

        function drawReverseSnake() {
            for (let i = 0; i < reverseSnake.length; i++) {
                if (i === 0) {
                    drawReverseSnakeHead(reverseSnake[i].x, reverseSnake[i].y);
                } else {
                    ctx.fillStyle = i % 2 === 0 ? "#FFFFFF" : "#000000";
                    ctx.beginPath();
                    ctx.arc(reverseSnake[i].x + box / 2, reverseSnake[i].y + box / 2, box / 2, 0, Math.PI * 2);
                    ctx.fill();
                }
            }
        }

        // Function to draw a reverse snake head
        function drawReverseSnakeHead(x, y) {
            ctx.fillStyle = "#FFD966";
            ctx.beginPath();
            ctx.arc(x + box / 2, y + box / 2, box / 2 + 5, 0, Math.PI * 2);
            ctx.fill();

            ctx.fillStyle = "#021527";
            ctx.beginPath();
            if (direction === "UP") {
                // Position eyes above the head
                ctx.arc(x + box / 2 - 5, y + box / 2 - 15, 3, 0, Math.PI * 2); // Left eye
                ctx.arc(x + box / 2 + 5, y + box / 2 - 15, 3, 0, Math.PI * 2); // Right eye
            } else if (direction === "DOWN") {
                // Position eyes below the head
                ctx.arc(x + box / 2 - 5, y + box / 2 + 15, 3, 0, Math.PI * 2); // Left eye
                ctx.arc(x + box / 2 + 5, y + box / 2 + 15, 3, 0, Math.PI * 2); // Right eye
            } else if (direction === "LEFT") {
                // Position eyes to the left of the head
                ctx.arc(x + box / 2 - 15, y + box / 2 - 5, 3, 0, Math.PI * 2); // Left eye
                ctx.arc(x + box / 2 - 15, y + box / 2 + 5, 3, 0, Math.PI * 2); // Right eye
            } else if (direction === "RIGHT") {
                // Position eyes to the right of the head
                ctx.arc(x + box / 2 + 15, y + box / 2 - 5, 3, 0, Math.PI * 2); // Left eye
                ctx.arc(x + box / 2 + 15, y + box / 2 + 5, 3, 0, Math.PI * 2); // Right eye
            }
            ctx.fill();

            ctx.fillStyle = "#FFD966";
            ctx.beginPath();
            if (direction === "UP") {
                ctx.arc(x + box / 2 - 5, y + box / 2 - 15, 1.5, 0, Math.PI * 2); // Left pupil
                ctx.arc(x + box / 2 + 5, y + box / 2 - 15, 1.5, 0, Math.PI * 2); // Right pupil
            } else if (direction === "DOWN") {
                ctx.arc(x + box / 2 - 5, y + box / 2 + 15, 1.5, 0, Math.PI * 2); // Left pupil
                ctx.arc(x + box / 2 + 5, y + box / 2 + 15, 1.5, 0, Math.PI * 2); // Right pupil
            } else if (direction === "LEFT") {
                ctx.arc(x + box / 2 - 15, y + box / 2 - 5, 1.5, 0, Math.PI * 2); // Left pupil
                ctx.arc(x + box / 2 - 15, y + box / 2 + 5, 1.5, 0, Math.PI * 2); // Right pupil
            } else if (direction === "RIGHT") {
                ctx.arc(x + box / 2 + 15, y + box / 2 - 5, 1.5, 0, Math.PI * 2); // Left pupil
                ctx.arc(x + box / 2 + 15, y + box / 2 + 5, 1.5, 0, Math.PI * 2); // Right pupil
            }
            ctx.fill();

            // Draw the tongue
            ctx.strokeStyle = "#FF6347";
            ctx.beginPath();
            if (direction === "UP") {
                ctx.moveTo(x + box / 2, y + box / 2 - box / 2 - 10); // Tongue start above the head
                ctx.lineTo(x + box / 2, y + box / 2 - box / 2 - 20); // Draw tongue upward
            } else if (direction === "DOWN") {
                ctx.moveTo(x + box / 2, y + box / 2 + box / 2); // Tongue start below the head
                ctx.lineTo(x + box / 2, y + box / 2 + box / 2 + 10); // Draw tongue downward
            } else if (direction === "LEFT") {
                ctx.moveTo(x + box / 2 - box / 2 - 10, y + box / 2); // Tongue start left of the head
                ctx.lineTo(x + box / 2 - box / 2 - 20, y + box / 2); // Draw tongue leftward
            } else if (direction === "RIGHT") {
                ctx.moveTo(x + box / 2 + box / 2, y + box / 2); // Tongue start right of the head
                ctx.lineTo(x + box / 2 + box / 2 + 10, y + box / 2); // Draw tongue rightward
            }
            ctx.stroke();
        }

        // Update score display
        function updateScoreDisplay() {
            scoreDisplay.innerHTML = score;
        }

        function showResetButton() {
            resetButton.style.display = 'inline-block';
            exitButton.style.display = 'inline-block';
        }

        function resetGame() {
            initGame();
        }

        function exitGame() {
            window.location.href = '/';
        }

        function submitScore(score) {
            alert('Game Over! Your score is ' + score);
        }

        initGame(); // Initialize the game when the page loads
    </script>
@endpush
