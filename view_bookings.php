<?php
session_start();
// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit;
}
// connect to the database
include('config.php');

$conn = mysqli_connect($host, $username, $password, $database);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // check if the form was submitted to delete a booking
    if (isset($_POST["delete"])) {
        $id = $_POST["id"];
        $sql = "DELETE FROM bookings WHERE id=$id";
        if (mysqli_query($conn, $sql)) {
            $successMessage = "Prenotazione cancellata con successo!.";
        } else {
            echo "Si è verificato un errore nella cancellazione: " . mysqli_error($conn);
        }
    }
    // check if the form was submitted to update a booking
if (isset($_POST["update"])) {
    $id = $_POST["id"];
    $room_id = isset($_POST["room_id"]) ? $_POST["room_id"] : '';
    $user_id = isset($_POST["user_id"]) ? $_POST["user_id"] : '';
    $start_date = $_POST["start_date"];
    $end_date = $_POST["end_date"];
    $checkin_time = $_POST["checkin_time"];
    $checkout_time = $_POST["checkout_time"];
    $user_name = $_POST["user_name"];
    $user_email = $_POST["user_email"];
    $num_guests = $_POST["num_guests"];

    $sql = "UPDATE bookings SET start_date='$start_date', end_date='$end_date', checkin_time='$checkin_time', checkout_time='$checkout_time', user_name='$user_name', user_email='$user_email', num_guests=$num_guests";

    if (!empty($room_id)) {
        $sql .= ", room_id=$room_id";
    }
    if (!empty($user_id)) {
        $sql .= ", user_id=$user_id";
    }

    $sql .= " WHERE id=$id";

    if (mysqli_query($conn, $sql)) {
        $successMessage = "Aggiornamento effettuato.";
    } else {
        echo "Error updating booking: " . mysqli_error($conn);
    }    
}
}
// query all bookings from the database
$sql = "SELECT b.id, r.name AS room_name, u.user_name, b.start_date, b.end_date, b.checkin_time, b.checkout_time, b.user_email, b.num_guests 
        FROM bookings b
        INNER JOIN rooms r ON b.room_id = r.id
        INNER JOIN users u ON b.user_id = u.id";

$result = mysqli_query($conn, $sql);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Visualizza prenotazioni</title>
    <link rel="stylesheet" href="http://localhost/room_res/css/viewbooking.css">
    <style>
    body {
    max-width: 73%;
    margin: 0 auto;
    }
    </style>
</head>
<body>
<h1 style="display: inline-block;">Visualizza prenotazioni</h1>
  <a href="dashboard.php" style="position: relative; top: 52px; left: 972px; background-color: green; color: white; padding: 8px; text-decoration: none;">Torna Indietro</a>
    <!--<h2>Visualizza prenotazioni</h2>//-->
    <table>
        <tr>
            <th></th>
            <th>Camera</th>
            <th>Data inizio</th>
            <th>Data fine</th>
            <th> Orario Check-in</th>
            <th> Orario Check-out</th>
            <th>Nome Ospite</th>
            <th>Email</th>
            <th>Numero ospiti</th>
            <th>Azioni</th>
        </tr>
        <?php
        // query all bookings from the database
        $sql = "SELECT bookings.id, rooms.name AS room_name, start_date, end_date, bookings.checkin_time, bookings.checkout_time, user_name, user_email, num_guests 
        FROM bookings 
        INNER JOIN rooms ON bookings.room_id = rooms.id";
        
        /*$sql = "SELECT bookings.id, rooms.name AS room_name, start_date, end_date, checkin_time, checkout_time, user_name, user_email, num_guests FROM bookings INNER JOIN rooms ON bookings.room_id = rooms.id";*/
        
        $result = mysqli_query($conn, $sql);

        while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <td><?php /*echo $row["id"]; */?></td>
                    <td><?php echo $row["room_name"]; ?></td>
                    <td><input type="date" name="start_date" value="<?php echo $row["start_date"]; ?>"></td>
                    <td><input type="date" name="end_date" value="<?php echo$row["end_date"]; ?>"></td>
                    <td><input type="time" name="checkin_time" value="<?php echo $row["checkin_time"]; ?>"></td>
                    <td><input type="time" name="checkout_time" value="<?php echo $row["checkout_time"]; ?>"></td>
                    <td><input type="text" name="user_name" value="<?php echo $row["user_name"]; ?>"></td>
                    <td><input type="email" name="user_email" value="<?php echo $row["user_email"]; ?>"></td>
                    <td><input type="number" name="num_guests" value="<?php echo $row["num_guests"]; ?>"></td>
                    <td>
                        <input type="hidden" name="id" value="<?php echo $row["id"]; ?>">
                        <input type="submit" name="update" value="Aggiorna">
                        <input type="submit" name="delete" value="Cancella" style="background-color: red;" onclick="return confirm('Sei sicuro di volere eliminare questa prenotazione?');">
                    </td>
                </form>
            </tr>
        <?php } ?>
    </table>
    <?php if (isset($successMessage)) : ?>
        <script>
            alert("<?php echo $successMessage; ?>");
        </script>
    <?php endif; ?>
</body>
</html>
