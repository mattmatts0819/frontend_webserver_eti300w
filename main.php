<?php
// 1. Read the team name from the query string (default to “PHI” if not set)
$selectedTeam = isset($_GET['team']) && trim($_GET['team']) !== ''
    ? trim($_GET['team'])
    : 'PHI';

// 2. Build the API URL with the team parameter
$apiUrl = "http://44.212.9.251/api/roster-fetch.php?team=" . urlencode($selectedTeam);

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

$team   = $data['body']['team'] ?? $selectedTeam;
$roster = $data['body']['roster'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Redzone Statistics</title>
<link rel="stylesheet" href="styles.css">

  <style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    form { 
      border: 1px solid #ccc; 
      padding: 15px; 
      width: 90%; 
      max-width: 500px; 
      margin: 10px auto; 
      background: #f9f9f9;
    }
    form label { display: block; margin-top: 10px; }
    form input { width: 100%; padding: 8px; box-sizing: border-box; }
    form button { margin-top: 15px; padding: 10px 20px; }
    table { border-collapse: collapse; width: 90%; margin: 20px auto; }
    th, td { border: 1px solid #ccc; padding: 8px 12px; text-align: center; }
    th { background-color: #f2f2f2; }
    caption { font-size: 1.5em; margin: 10px 0; }
  </style>
</head>
<body>
<img src="redzone_logo.jpg" alt="company logo" style="width:300px;height:300px; display: block; margin-left: auto; margin-right: auto;">
  <!-- 1) New Preferences Form -->
  <form method="post" action="http://44.212.9.251/api/db-create.php">
    <h2>Submit Your Preferences</h2>
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" required />

    <label for="location">Location:</label>
    <input type="text" id="location" name="location" required />

    <label for="favorite_team">Favorite Team:</label>
    <input type="text" id="favorite_team" name="favorite_team" required />

    <label for="favorite_player">Favorite Player:</label>
    <input type="text" id="favorite_player" name="favorite_player" required />

    <button type="submit">Submit Preferences</button>
  </form>

  <!-- 2) Existing Team Lookup Form -->
  <form method="get" action="">
    <h2>Load Roster by Team</h2>
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

  <!-- 3) Roster Table -->
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
          <td><?php echo htmlspecialchars($player['longName'] ?? $player['espnName']); ?></td>
          <td><?php echo htmlspecialchars($player['pos']); ?></td>
          <td><?php echo htmlspecialchars($player['school']); ?></td>
          <td><?php echo htmlspecialchars($player['age']); ?></td>
          <td><?php echo htmlspecialchars($player['lastGamePlayed']); ?></td>
          <td><?php echo htmlspecialchars($player['stats']['gamesPlayed'] ?? 'N/A'); ?></td>
          <td>
            <?php 
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
