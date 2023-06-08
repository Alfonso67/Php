<?php
session_start();
// Check if the user is logged in
/*if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit;
}*/
include 'config.php';

// Retrieve the search parameters from the request
$name = $_GET['name'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

// Check if the reset button was clicked
if (isset($_GET['reset'])) {
    // Clear the search parameters
    $name = '';
    $start_date = '';
    $end_date = '';
}

// Check if the form was submitted
if (isset($_GET['submit'])) {
    // Check if all fields are filled
    if (!$name || !$start_date || !$end_date) {
        echo "<script>alert('Tutti i campi devono essere compilati');</script>";
    } else {
        // Convert the date strings to MySQL date format
        if ($start_date && $end_date) {
            $start_date_mysql = date('Y-m-d', strtotime($start_date));
            $end_date_mysql = date('Y-m-d', strtotime($end_date));
        }

        // Check if the start date is in the past
        //if ($start_date && strtotime($start_date) < strtotime(date('Y-m-d'))) {
            if ($start_date && strtotime($start_date) < strtotime(date('Y-m-d'))) {
                $formattedStartDate = date('d-m-Y', strtotime($start_date));
            echo "<script>alert('Non è possibile fare una prenotazione dei giorni passati.');</script>";
        } else {
            // Build the SQL query
            $sql = "SELECT r.*, COUNT(b.id) AS num_bookings, MAX(b.end_date) AS max_end_date
            FROM rooms r
            LEFT JOIN bookings b ON r.id = b.room_id AND 
            ((b.start_date <= '$start_date' AND b.end_date >= '$start_date') OR 
            (b.start_date <= '$end_date' AND b.end_date >= '$end_date') OR 
            (b.start_date >= '$start_date' AND b.end_date <= '$end_date' AND TIME(b.end_date) >= '10:00:00'))
            WHERE r.name LIKE '%$name%' 
            GROUP BY r.id";
            // Execute the query
            $result = mysqli_query($conn, $sql);

            // Check if there are any rooms available
            if (mysqli_num_rows($result) == 0) {
                echo 'Nessuna disponibilità per le date inserite.';
            } else {
                // Display the search results
                while ($row = mysqli_fetch_assoc($result)) {
                    // Check if the room is available for the selected dates
                    $available = $row['num_bookings'] == 0;
                    $color = $available ? 'green' : 'red';
                    // Display the room details with color-coded availability
                    echo "<div class='room-box' style='border-color: $color;'>
    <h3>{$row['name']}</h3>
    <p>{$row['description']}</p>
    <p><strong>Disponibile:</strong> <span class='$color'>" . ($available ? 'Si' : 'No') . "</span></p>";
if ($row['max_end_date']) {
    $maxEndDate = date('d-m-Y H:i', strtotime($row['max_end_date']));
    $remainingTime = strtotime($row['max_end_date']) - time();
    $remainingHours = floor($remainingTime / 3600);
    $remainingMinutes = floor(($remainingTime % 3600) / 60);
    if ($remainingTime > 0) {
        $remainingTimeString = ($remainingHours > 0) ? "$remainingHours ore $remainingMinutes minuti" : "$remainingMinutes minuti";
        echo "<p><strong>Termine per la prossima prenotazione:</strong> $maxEndDate<br><strong>Tempo rimanente:</strong> $remainingTimeString</p>";
    } /*else {
        echo "<strong><p>Termine per la prossima prenotazione:</strong> $maxEndDate<br>Tempo rimanente:Scaduto</p>";
    }*/
} else {
    /*echo "<p>Termine per la prossima prenotazione: N/A</p>";*/
}

echo "<!--<button onclick='showPopup(\"{$row['name']}\", \"{$row['max_end_date']}\")'>Dettagli</button>//--></div>";

                }
            }
        }
    }
}

// Get the current date and time
$currentDate = date('Y-m-d H:i:s');
// Delete expired bookings
$deleteSql = "DELETE FROM bookings WHERE end_date <= '$currentDate'";
mysqli_query($conn, $deleteSql);

// Close the database connection
mysqli_close($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Room Reservation System</title>
<link rel="stylesheet" href="http://localhost/room_res/css/booking.css">
</head>
<style>
    body {
    max-width: 70%;
    margin: 0 auto;
    }
    </style>
<body>
    <header>
    <h1><center>Verifica disponibilità camere</center></h1>
    <a href="<?php echo isset($_SESSION['loggedin']) && $_SESSION['loggedin'] ? 'dashboard.php' : 'index.php'; ?>" style="position: relative; top: 10px; left: 755px; background-color: green; color: white; padding: 10px; text-decoration: none;">Torna Indietro</a>
    </header> 
    <form action="" method="GET">
        <label for="name">Nome Camera:</label>
        <input type="text" name="name" id="name" value="<?php echo $name; ?>">
        <br>
        <label for="start_date">Data inizio:</label>
        <input type="date" name="start_date" id="start_date" value="<?php echo $start_date; ?>">
        <br>
        <label for="end_date">Data fine:</label>
        <input type="date" name="end_date" id="end_date" value="<?php echo $end_date; ?>">
        <br>
        <input type="submit" name="submit" value="Cerca">
        <input type="submit" name="reset" value="Cancella ricerca">
    </form>

    <?php
    // Display the error message in a popup without redirecting the page
    if (isset($_GET['submit']) && strtotime($start_date) < strtotime(date('Y-m-d'))) {
        echo '<script>alert("Non è possibile fare una prenotazione dei giorni passati.");</script>';
    }
    ?>

<script>
   function showPopup(roomName, endTime) {
  var endTimeFormatted = new Date(endTime).toLocaleString('it-IT');
  var checkOutTime = new Date(endTime);
  checkOutTime.setHours(10, 0, 0); // Set the check-out time to 10:00 AM
  var currentTime = new Date();
  var timeRemaining = checkOutTime.getTime() - currentTime.getTime();
  if (timeRemaining <= 0) {
    alert(`La camera '${roomName}' non è disponibile per la prenotazione.`);
  } else {
    var minutesRemaining = Math.floor(timeRemaining / (1000 * 60));
    var hoursRemaining = Math.floor(minutesRemaining / 60);
    var minutes = minutesRemaining % 60;
    alert(`La camera '${roomName}' sarà disponibile a partire da ${endTimeFormatted}\nTempo rimanente: ${hoursRemaining} ore ${minutes} minuti`);
  }
}
</script>
</body>
</html>
