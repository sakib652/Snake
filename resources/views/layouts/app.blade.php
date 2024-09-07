<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Snake Game')</title>
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
    @stack('styles')
</head>

<body>
    @yield('content')

    @stack('scripts')
</body>

</html>
