<?php
session_start();
// Check if the user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Calendario</title>
  <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css' rel='stylesheet' />
  <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.print.min.css' rel='stylesheet' media='print' />
  <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js'></script>
  <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
  <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js'></script>
  <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/locale/it.js'></script>
  <script>
    $(document).ready(function() {
      // Initialize the calendar
      $('#calendar').fullCalendar({
        // Set the calendar options
        defaultView: 'month',
        weekends: true,
        dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mer', 'Gio', 'Ven', 'Sab'],
        monthNames: ['Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno', 'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre'],
        header: {
          left: 'prev,next today',
          center: 'title',
          right: 'month,basicWeek,basicDay'
        },
        dayRender: function(date, cell) {
          var today = moment().startOf('day');
          var renderedDate = moment(date).startOf('day');

          if (renderedDate < today) {
            // Mark the passed days as unavailable
            cell.addClass('unavailable-day');
            cell.css('cursor', 'not-allowed');
            cell.attr('title', 'Questa data non è disponibile per la prenotazione');
          }
        },
        dayClick: function(date, jsEvent, view) {
          // Get the clicked date
          var clickedDate = moment(date).format('YYYY-MM-DD');

          // Check if the clicked date falls within a booked period
          var event = $('#calendar').fullCalendar('clientEvents', function(event) {
            var eventStart = moment(event.start).startOf('day');
            var eventEnd = moment(event.end).subtract(1, 'day').startOf('day');
            return event.rendering === 'background' && clickedDate >= eventStart.format('YYYY-MM-DD') && clickedDate <= eventEnd.format('YYYY-MM-DD');
          });

          if (event.length > 0) {
            // Display a message that the room is not available for booking
            alert('The room is not available for booking on ' + clickedDate);
          } else if (moment(date).startOf('day') < moment().startOf('day')) {
            // Display a message that the selected date has passed
            alert('You cannot book a past date.');
          } else {
            // Navigate to the search booking page
            window.location.href = 'create_booking.php?start_date=' + moment(date).format('DD-MM-YYYY');
          }
        },
        events: [
          <?php
          // Include the config file to connect to the database
          include 'config.php';

          // Query the bookings table for the booked dates
          $sql = "SELECT * FROM bookings";
          $result = mysqli_query($conn, $sql);
          while ($row = mysqli_fetch_assoc($result)) {
            // Format the start and end dates for FullCalendar
            $start = date('Y-m-d', strtotime($row['start_date']));
            $end = date('Y-m-d', strtotime($row['end_date'] . ' + 1 day'));

            // Add the booked dates to the events array for FullCalendar
            echo "{ title: 'Booked', start: '$start', end: '$end', color: 'red' },";
          }

          // Close the database connection
          mysqli_close($conn);
          ?>
        ]
      });
    });
  </script>
   <style>
    /* Custom styles for the calendar */
    #calendar {
      max-width: 800px; /* Set the maximum width of the calendar container */
      margin: 0 auto; /* Center align the calendar horizontally */
    }
  </style>
</head>
<body>
<center>
  <h1 style="display: inline-block;">Calendario prenotazioni</h1>
  <a href="dashboard.php" style="position: relative; top: -10px; left: 100px; background-color: green; color: white; padding: 10px; text-decoration: none;">Torna Indietro</a>
</center>

  <div id='calendar'></div>
</body>
</html>
