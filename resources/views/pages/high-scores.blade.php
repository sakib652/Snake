<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>High Scores</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f5f5f5;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .container {
            text-align: center;
        }

        h1 {
            font-size: 2.5em;
            color: #333;
        }

        table {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            border-collapse: collapse;
        }

        th, td {
            padding: 15px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 18px;
            color: white;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>High Scores</h1>
        <table>
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Score</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($scores as $index => $score)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $score->score }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <a href="/" class="back-button">Back to Home</a>
    </div>
</body>

</html>
