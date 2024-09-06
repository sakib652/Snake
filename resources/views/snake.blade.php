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
            /* Light blue background to match the landing page */
            margin: 0;
            flex-direction: column;
            font-family: 'Changa one', Arial, sans-serif;
        }

        canvas {
            background-color: #021527;
            /* Soft blue, matching the playful tone of the landing page */
            border: 5px solid #FFD966;
            /* Bright yellow border to make it stand out */
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
            /* Dark blue text to match the theme */
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
            /* Bright orange to match landing page tones */
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
        const scoreDisplay = document.getElementById('score'); // Reference to the score display

        const box = 20;
        let snake;
        let food;
        let score;
        let direction;
        let game;

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

            // Draw Snake with more playful design
            for (let i = 0; i < snake.length; i++) {
                if (i === 0) {
                    // Draw head (larger, playful, with eyes)
                    drawSnakeHead(snake[i].x, snake[i].y);
                } else {
                    // Draw body segments (rounder, playful)
                    drawSnakeBody(snake[i].x, snake[i].y, i);
                }
            }

            // Draw Food (bright green apple-like circle)
            ctx.fillStyle = "#006400"; // Dark green for the food
            ctx.beginPath();
            ctx.arc(food.x + box / 2, food.y + box / 2, box / 2, 0, Math.PI * 2);
            ctx.fill();

            let snakeX = snake[0].x;
            let snakeY = snake[0].y;

            // Move the snake based on the direction
            if (direction == "LEFT") snakeX -= box;
            if (direction == "UP") snakeY -= box;
            if (direction == "RIGHT") snakeX += box;
            if (direction == "DOWN") snakeY += box;

            // Check if snake eats the food
            if (snakeX == food.x && snakeY == food.y) {
                score++;
                updateScoreDisplay(); // Update score when snake eats food
                food = {
                    x: Math.floor(Math.random() * 19 + 1) * box,
                    y: Math.floor(Math.random() * 19 + 1) * box
                };
            } else {
                snake.pop(); // Remove the last part of the snake
            }

            let newHead = {
                x: snakeX,
                y: snakeY
            };

            // Check for collisions (walls or itself)
            if (snakeX < 0 || snakeX >= canvas.width || snakeY < 0 || snakeY >= canvas.height || collision(newHead, snake)) {
                clearInterval(game);
                submitScore(score);
                showResetButton();
            } else {
                snake.unshift(newHead); // Add new head at the front of the snake
            }
        }

        // Function to draw a playful snake head
        function drawSnakeHead(x, y) {
            // Head base (larger circle)
            ctx.fillStyle = "#FF4500"; // Bright orange
            ctx.beginPath();
            ctx.arc(x + box / 2, y + box / 2, box / 2 + 5, 0, Math.PI * 2);
            ctx.fill();

            // Eyes
            ctx.fillStyle = "white";
            ctx.beginPath();
            ctx.arc(x + box / 2 - 5, y + box / 2 - 5, 3, 0, Math.PI * 2); // Left eye
            ctx.arc(x + box / 2 + 5, y + box / 2 - 5, 3, 0, Math.PI * 2); // Right eye
            ctx.fill();

            // Pupils
            ctx.fillStyle = "black";
            ctx.beginPath();
            ctx.arc(x + box / 2 - 5, y + box / 2 - 5, 1.5, 0, Math.PI * 2); // Left pupil
            ctx.arc(x + box / 2 + 5, y + box / 2 - 5, 1.5, 0, Math.PI * 2); // Right pupil
            ctx.fill();

            // Tongue (optional)
            ctx.strokeStyle = "#FF6347"; // Red tongue
            ctx.beginPath();
            ctx.moveTo(x + box / 2, y + box); // Start of the tongue
            ctx.lineTo(x + box / 2, y + box + 10); // End of the tongue
            ctx.stroke();
        }

        // Function to draw a snake body segment
        function drawSnakeBody(x, y, i) {
            ctx.fillStyle = i % 2 === 0 ? "#FF4500" : "#FFD966"; // Alternating colors for body segments
            ctx.beginPath();
            ctx.arc(x + box / 2, y + box / 2, box / 2, 0, Math.PI * 2);
            ctx.fill();
        }

        // Function to update the score display
        function updateScoreDisplay() {
            scoreDisplay.innerHTML = score; // Update the score on the screen
        }

        function showResetButton() {
            resetButton.style.display = 'inline-block';
            exitButton.style.display = 'inline-block';
        }

        function resetGame() {
            initGame();
        }

        function exitGame() {
            window.location.href = '/'; // Redirect to homepage (change URL as needed)
        }

        function submitScore(score) {
            alert('Game Over! Your score is ' + score);
        }

        initGame(); // Initialize the game when the page loads
    </script>
</body>

</html>
