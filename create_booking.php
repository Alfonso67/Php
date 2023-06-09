<?php
session_start();
// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit;
}
// Check if the form is submitted
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Retrieve user input from the form
  $room_id = $_POST["room_id"];
  $start_date = $_POST["start_date"];
  $start_time = $_POST["start_time"];
  $end_date = $_POST["end_date"];
  $end_time = $_POST["end_time"];
  $user_name = $_POST["user_name"];
  $user_email = $_POST["user_email"];
  $num_guests = $_POST["num_guests"];

  // Validate start and end dates
  $current_date = date("Y-m-d");
if ($start_date < $current_date) {
  echo "<script>alert('Errore: la data del check-in non può essere nel passato.');</script>";
  echo "<script>window.history.back();</script>"; // Go back to the previous page
  exit(); // Stop executing further
} elseif ($end_date < $start_date) {
  echo "<script>alert('Errore: la data di check-out non può essere precedente alla data di check-in.');</script>";
  echo "<script>window.history.back();</script>"; // Go back to the previous page
  exit(); // Stop executing further
} else {
  // Calculate the checkout time
  $checkout_time = date("H:i", strtotime($end_time) + (24 * 60 * 60));

  // Check for overlapping bookings
  $sql = "SELECT * FROM bookings WHERE room_id = '$room_id' AND (end_date > '$start_date' OR (end_date = '$start_date' AND end_time > '$start_time'))";
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    echo "<script>alert('Errore: l\'appartamento è già prenotato nelle date e negli orari selezionati.');</script>";
    echo "<script>window.history.back();</script>"; // Go back to the previous page
    exit(); // Stop executing further
  }
}
    // Prepare and execute the SQL query
    $sql = "INSERT INTO bookings (room_id, start_date, end_date, checkin_time, checkout_time, user_name, user_email, num_guests) VALUES ('$room_id', '$start_date', '$end_date', '$start_time', '$checkout_time', '$user_name', '$user_email', '$num_guests')";

    if ($conn->query($sql) === TRUE) {
      echo "<script>alert('Prenotazione creata con successo');</script>";
      echo "<script>window.location.href = 'view_bookings.php';</script>";
    } else {
      echo "<script>alert('Error: " . $conn->error . "');</script>";
    }

  // Close the database connection
  $conn->close();
} else {
  // Display the booking form
  ?>
  <head>
   <link rel="stylesheet" href="http://localhost/room_res/css/booking.css">
  </head>
  <style>
    input[type="reset"].input-field.reset-button {
      background-color: #333;
      color: white;
      cursor: pointer;
    }

    input[type="reset"].input-field.reset-button:hover {
      background-color: #5f5e5e;
    }
  </style>
  <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="booking-form" onsubmit="return validateForm()">
    <label for="room_id"><h2>Prenotazione Camere:</h2></label>
    <h1 style="display: inline-block;"></h1>
    <a href="dashboard.php" style="position: relative; top: -22px; left: 130px; background-color: green; color: white; padding: 10px; text-decoration: none;">Torna Indietro</a>
    <select id="room_id" name="room_id" class="input-field">
      <?php
      // Retrieve the list of rooms
      $sql = "SELECT * FROM rooms";
      $result = $conn->query($sql);
      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          echo "<option value='" . $row["id"] . "'>" . $row["name"] . " <!--($" . $row["price"] . "/night)//-->" . "</option>";
        }
      }
      // Close the database connection
      $conn->close();
      ?>
    </select><br><br>

    <label for="start_date">Check-in date:</label>
    <input type="date" id="start_date" name="start_date" required><br><br>

    <label for="start_time" style="color:red">Check-in time:</label>
    <input type="time" id="start_time" name="start_time" style="color:red" required><br><br>

    <label for="end_date">Check-out date:</label>
    <input type="date" id="end_date" name="end_date" required><br><br>

    <label for="end_time" style="color:red">Check-out time:</label>
    <input type="time" id="end_time" name="end_time" style="color:red" required><br><br>

    <label for="user_name">Name:</label>
    <input type="text" id="user_name" name="user_name" required><br><br>

    <label for="user_email">Email:</label>
    <input type="email" id="user_email" name="user_email" required><br><br>

    <label for="num_guests">Number of guests:</label>
    <input type="number" id="num_guests" name="num_guests" min="1" max="10" style="color:red" required><br><br>

    <input type="submit" value="Prenota" class="submit-button">
    <input type="reset" value="Cancella" class="input-field reset-button">
  </form>

  <script>
    // JavaScript function to display error messages in a popup window
    function displayErrorPopup(message) {
      alert(message);
    }

    // JavaScript function to prevent form submission if there are errors
    function validateForm() {
      var startDate = document.getElementById("start_date").value;
      var endDate = document.getElementById("end_date").value;
      var startTime = document.getElementById("start_time").value;
      var endTime = document.getElementById("end_time").value;
      // Validate start and end dates
      var currentDate = new Date().toISOString().slice(0, 10);
      if (startDate < currentDate) {
        displayErrorPopup("Error: La data non può essere quella trascorsa.");
        return false; // Prevent form submission
      } else if (endDate < startDate) {
        displayErrorPopup("Error: La data di check-out non può essere precedente alla data di check-in.");
        return false; // Prevent form submission
      }
      // Validate start and end times
      var currentDateTime = new Date().toISOString().slice(0, 16);
      var startDateTime = startDate + "T" + startTime;
      var endDateTime = endDate + "T" + endTime;
      if (startDateTime < currentDateTime) {
        displayErrorPopup("Error: Check-in time cannot be in the past.");
return false; // Prevent form submission
} else if (endDateTime <= startDateTime) {
displayErrorPopup("Error: Check-out time cannot be earlier than or equal to the check-in time.");
return false; // Prevent form submission
}
}
//email validate
 JavaScript function to validate email format
  function validateEmail(email) {
    // Regular expression to validate email format
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }
    var userEmail = document.getElementById("user_email").value;
    // Validate email format
    if (!validateEmail(userEmail)) {
      displayErrorPopup("Error: Invalid email format.");
      return false; // Prevent form submission
    }
  }
</script>
  <?php
}
?>

