<?php
// index.php

// 1. Read the team name from the query string (default to “PHI” if not set)
$selectedTeam = isset($_GET['team']) && trim($_GET['team']) !== ''
    ? trim($_GET['team'])
    : 'PHI';

// 2. Build the API URL with the team parameter
//    Your middleware should accept “team” as a GET parameter and forward it to the external API
$apiUrl = "http://54.205.236.26/api/roster-fetch.php?team=" . urlencode($selectedTeam);

// 3. Fetch the JSON data
$jsonData = @file_get_contents($apiUrl);
if ($jsonData === false) {
    die("Error: Unable to fetch data from the API for team “" . htmlspecialchars($selectedTeam) . "”.");
}

// 4. Decode the JSON into an associative array
$data = json_decode($jsonData, true);
if (!isset($data['body']['roster']) || !is_array($data['body']['roster'])) {
    die("Error: API did not return valid roster data.");
}

$team       = isset($data['body']['team'])   ? $data['body']['team']   : $selectedTeam;
$roster     = $data['body']['roster'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Team Roster Lookup</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    form { text-align: center; margin-bottom: 20px; }
    table { border-collapse: collapse; width: 90%; margin: 0 auto; }
    th, td { border: 1px solid #ccc; padding: 8px 12px; text-align: center; }
    th { background-color: #f2f2f2; }
    caption { font-size: 1.5em; margin: 10px 0; }
  </style>
</head>
<body>

  <!-- Team Selection Form -->
  <form method="get" action="">
    <label for="team">Enter Team Abbreviation:</label>
    <input
      type="text"
      id="team"
      name="team"
      value="<?php echo htmlspecialchars($selectedTeam); ?>"
      placeholder="e.g. PHI, DAL, NE"
      required
    />
    <button type="submit">Load Roster</button>
  </form>

  <!-- Roster Table -->
  <h1 style="text-align:center;">Roster for <?php echo htmlspecialchars($team); ?></h1>

  <table>
    <caption>Players</caption>
    <thead>
      <tr>
        <th>Jersey #</th>
        <th>Name</th>
        <th>Position</th>
        <th>School</th>
        <th>Age</th>
        <th>Last Game</th>
        <th>Games Played</th>
        <th>Pass Yds</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($roster as $player): ?>
        <tr>
          <td><?php echo htmlspecialchars($player['jerseyNum']); ?></td>
          <td><?php 
            // prefer longName, fallback to espnName
            echo htmlspecialchars($player['longName'] ?? $player['espnName']);
          ?></td>
          <td><?php echo htmlspecialchars($player['pos']); ?></td>
          <td><?php echo htmlspecialchars($player['school']); ?></td>
          <td><?php echo htmlspecialchars($player['age']); ?></td>
          <td><?php echo htmlspecialchars($player['lastGamePlayed']); ?></td>
          <td>
            <?php echo htmlspecialchars($player['stats']['gamesPlayed'] ?? 'N/A'); ?>
          </td>
          <td>
            <?php 
              // Only show passing yards if present
              echo isset($player['stats']['Passing']['passYds'])
                   ? htmlspecialchars($player['stats']['Passing']['passYds'])
                   : 'N/A';
            ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

</body>
</html>

