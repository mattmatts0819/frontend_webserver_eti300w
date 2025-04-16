<?php
// Filename: index.php

// URL of your middleware server endpoint that returns the JSON data.
$apiUrl = "http://107.23.254.8/api/nfl-conn.php";

// Attempt to fetch the data.
$jsonData = file_get_contents($apiUrl);

// Check if the API call was successful.
if ($jsonData === false) {
    die("Error: Unable to fetch data from the API.");
}

// Decode the JSON data to a PHP array.
$data = json_decode($jsonData, true);

// Check if the JSON could not be decoded.
if ($data === null) {
    die("Error: Unable to decode JSON data.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>API Data Table</title>
    <style>
        /* Basic styles for the table */
        table {
            border-collapse: collapse;
            width: 80%;
            margin: 20px auto;
            font-family: Arial, sans-serif;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
        }
        th {
            background-color: #f2f2f2;
        }
        caption {
            font-size: 1.5em;
            margin: 10px;
        }
    </style>
</head>
<body>
    <table>
        <caption>API Data</caption>
        <thead>
            <tr>
                <?php
                // If the data is a list of associative arrays, extract the headers from the first element.
                if (isset($data[0]) && is_array($data[0])) {
                    foreach (array_keys($data[0]) as $header) {
                        echo "<th>" . htmlspecialchars($header) . "</th>";
                    }
                } elseif (is_array($data)) {
                    // If data is a single associative array.
                    foreach (array_keys($data) as $header) {
                        echo "<th>" . htmlspecialchars($header) . "</th>";
                    }
                }
                ?>
            </tr>
        </thead>
        <tbody>
            <?php
            // If data is a list of items.
            if (isset($data[0]) && is_array($data[0])) {
                foreach ($data as $row) {
                    echo "<tr>";
                    foreach ($row as $value) {
                        echo "<td>" . htmlspecialchars($value) . "</td>";
                    }
                    echo "</tr>";
                }
            } elseif (is_array($data)) {
                // If data is a single associative array, display its values in one row.
                echo "<tr>";
                foreach ($data as $value) {
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
                echo "</tr>";
            } else {
                echo "<tr><td colspan='100%'>No data available.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
