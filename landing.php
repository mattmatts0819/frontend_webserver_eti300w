<?php
// index.php

// URL to your middleware endpoint that returns the JSON data.
// For testing, you might also use a local file path such as 'data.json'.
$apiUrl = "http://107.23.254.8/api/nfl-conn.php";

// Attempt to get the JSON data.
$jsonData = file_get_contents($apiUrl);
if ($jsonData === false) {
    die("Error: Unable to fetch data from the API.");
}

// Decode the JSON string into a PHP array.
$data = json_decode($jsonData, true);
if ($data === null) {
    die("Error: Unable to decode JSON data.");
}

// Extract team information and roster list from the decoded data.
$team = isset($data['body']['team']) ? $data['body']['team'] : "Unknown Team";
$roster = isset($data['body']['roster']) ? $data['body']['roster'] : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Team Roster</title>
    <style>
        /* Basic table styling */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            text-align: center;
        }
        table {
            border-collapse: collapse;
            width: 90%;
            margin: 20px auto;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px 12px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        caption {
            font-size: 1.5em;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <h1>Team: <?php echo htmlspecialchars($team); ?></h1>
    <table>
        <caption>Roster</caption>
        <thead>
            <tr>
                <th>Jersey #</th>
                <th>Name</th>
                <th>Position</th>
                <th>School</th>
                <th>Age</th>
                <th>Last Game</th>
                <th>Games Played</th>
                <th>Passing Yards</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($roster)) : ?>
                <tr>
                    <td colspan="8">No roster data available.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($roster as $player): ?>
                    <tr>
                        <!-- Jersey Number -->
                        <td><?php echo htmlspecialchars($player['jerseyNum']); ?></td>
                        <!-- Name (using longName if available) -->
                        <td><?php echo htmlspecialchars(isset($player['longName']) ? $player['longName'] : $player['espnName']); ?></td>
                        <!-- Position -->
                        <td><?php echo htmlspecialchars($player['pos']); ?></td>
                        <!-- School -->
                        <td><?php echo htmlspecialchars($player['school']); ?></td>
                        <!-- Age -->
                        <td><?php echo htmlspecialchars($player['age']); ?></td>
                        <!-- Last Game Played -->
                        <td><?php echo htmlspecialchars($player['lastGamePlayed']); ?></td>
                        <!-- Games Played from stats -->
                        <td>
                            <?php 
                                echo isset($player['stats']['gamesPlayed']) 
                                    ? htmlspecialchars($player['stats']['gamesPlayed']) 
                                    : 'N/A'; 
                            ?>
                        </td>
                        <!-- Passing Yards if available; if not, show N/A -->
                        <td>
                            <?php 
                                if (isset($player['stats']['Passing']) && isset($player['stats']['Passing']['passYds'])) {
                                    echo htmlspecialchars($player['stats']['Passing']['passYds']);
                                } else {
                                    echo 'N/A';
                                }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
