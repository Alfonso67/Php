<?php
// check_bookings.php

include 'config.php';

$room_id = $_GET["room_id"];
$start_date = $_GET["start_date"];
$start_time = $_GET["start_time"];
$end_date = $_GET["end_date"];
$end_time = $_GET["end_time"];

// Check for overlapping bookings
$sql = "SELECT * FROM bookings WHERE room_id = '$room_id' AND end_date >= '$start_date' AND end_time > '$start_time'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
  // Overlapping booking found
  $response = array(
    "error" => true,
    "message" => "Error: The room is already booked during the selected dates and times."
  );
} else {
  // No overlapping booking found
  $response = array(
    "error" => false,
    "message" => ""
  );
}

echo json_encode($response);

$conn->close();
?>
