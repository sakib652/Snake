<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Snake Game</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #8ACDEA;
            margin: 0;
            flex-direction: column;
            font-family: 'Changa one', Arial, sans-serif;
        }

        canvas {
            background-color: #021527;
            border: 5px solid #FFD966;
            border-radius: 15px;
            position: relative;
        }

        #scoreContainer {
            margin-bottom: 20px;
            text-align: center;
            background-color: #FFD966;
            padding: 10px 20px;
            border-radius: 10px;
            color: #021527;
            font-size: 20px;
        }

        #scoreContainer h2 {
            margin: 0;
            font-size: 24px;
        }

        #score {
            font-size: 30px;
            font-weight: bold;
        }

        #resetButton,
        #exitButton {
            display: none;
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 18px;
            cursor: pointer;
            border: none;
            color: white;
            border-radius: 10px;
        }

        #resetButton {
            background-color: #FF5733;
        }

        #resetButton:hover {
            background-color: #E5533D;
        }

        #exitButton {
            background-color: #007bff;
            margin-left: 30px;
        }

        #exitButton:hover {
            background-color: #0056b3;
        }

        #buttons {
            display: flex;
            justify-content: center;
        }
    </style>
</head>

<body>
    <div id="scoreContainer">
        <h2>Score</h2>
        <div id="score">0</div>
    </div>

    <canvas id="snakeGame" width="400" height="400"></canvas>

    <div id="buttons">
        <button id="resetButton">Reset Game</button>
        <button id="exitButton">Exit</button>
    </div>

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

            ctx.fillStyle = "white";
            ctx.beginPath();
            ctx.arc(x + box / 2 - 5, y + box / 2 - 5, 3, 0, Math.PI * 2);
            ctx.arc(x + box / 2 + 5, y + box / 2 - 5, 3, 0, Math.PI * 2);
            ctx.fill();

            ctx.fillStyle = "black";
            ctx.beginPath();
            ctx.arc(x + box / 2 - 5, y + box / 2 - 5, 1.5, 0, Math.PI * 2);
            ctx.arc(x + box / 2 + 5, y + box / 2 - 5, 1.5, 0, Math.PI * 2);
            ctx.fill();

            ctx.strokeStyle = "#FF6347";
            ctx.beginPath();
            ctx.moveTo(x + box / 2, y + box);
            ctx.lineTo(x + box / 2, y + box + 10);
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

        // Function to draw the reverse snake's head
        function drawReverseSnakeHead(x, y) {
            ctx.fillStyle = "#FFD966";
            ctx.beginPath();
            ctx.arc(x + box / 2, y + box / 2, box / 2 + 5, 0, Math.PI * 2);
            ctx.fill();

            ctx.fillStyle = "#021527";
            ctx.beginPath();
            ctx.arc(x + box / 2 - 5, y + box / 2 - 5, 3, 0, Math.PI * 2);
            ctx.arc(x + box / 2 + 5, y + box / 2 - 5, 3, 0, Math.PI * 2);
            ctx.fill();

            ctx.fillStyle = "#FFD966";
            ctx.beginPath();
            ctx.arc(x + box / 2 - 5, y + box / 2 - 5, 1.5, 0, Math.PI * 2);
            ctx.arc(x + box / 2 + 5, y + box / 2 - 5, 1.5, 0, Math.PI * 2);
            ctx.fill();

            ctx.strokeStyle = "#FFD966";
            ctx.beginPath();
            ctx.moveTo(x + box / 2, y + box);
            ctx.lineTo(x + box / 2, y + box + 10);
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

</body>

</html>
