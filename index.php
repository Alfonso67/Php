<!DOCTYPE html>
<html>
<head>
    <title>Calendario</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/locale/it.js"></script>
    <link rel="stylesheet" href="http://localhost/room_res/css/calendar.css">
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
    var clickedDate = moment(date).format('DD-MM-YYYY');

    // Check if the clicked date falls within a booked period
    var event = $('#calendar').fullCalendar('clientEvents', function(event) {
        var eventStart = moment(event.start).startOf('day');
        var eventEnd = moment(event.end).subtract(1, 'day').startOf('day');
        return event.rendering === 'background' && clickedDate >= eventStart.format('DD-MM-YYYY') && clickedDate <= eventEnd.format('DD-MM-YYYY');
    });

    if (event.length > 0) {
        // Get the checkout date and time
        var checkoutDate = moment(event[0].end).subtract(1, 'day').format('DD-MM-YYYY');
        var checkoutTime = '10:00'; // Set the default checkout time to 10:00

        // Display the message with the checkout date and time
        var message = 'La camera non è disponibile. È prenotata fino al ' + checkoutDate + ' alle ' + checkoutTime;
        alert(message);
        // Redirect to the search_bookings.php page
        window.location.href = 'search_bookings.php';
    } else if (moment(date).startOf('day') < moment().startOf('day')) {
        // Display a message that the selected date has passed
        alert('Non è possibile fare una prenotazione dei giorni passati.');
    } else {
        // Display the availability message
        alert('La camera è disponibile');
        // Redirect to the search_bookings.php page
        window.location.href = 'search_bookings.php';
    }
},
eventAfterRender: function(event, element) {
    if (event.rendering === 'background') {
        var today = moment().startOf('day');
        var renderedDate = moment(event.start).startOf('day');

        if (renderedDate < today) {
            // Mark the passed days as unavailable
            element.addClass('unavailable-day');
            element.css('cursor', 'not-allowed');
            element.attr('title', 'Questa data non è disponibile per la prenotazione');
        } else {
            // Check if it is the last date of a booked room
            var isLastDate = renderedDate.isSame(moment(event.end).subtract(1, 'day').startOf('day'));

            // Create the custom HTML for the event element
            var customHTML = '<div class="availability-event">' +
                '<div class="' + (isLastDate ? 'split-part' : 'normal-part') + '"></div>' +
                '<div class="event-title" style="color: white;">' + event.title + '</div>' +
                '</div>';

            // Set the HTML content of the element
            element.html(customHTML);
        }
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
                $checkoutTime = isset($row['end_date']) ? date('H:i', strtotime($row['end_date'])) : ''; // Get the checkout time if it's not null
               // Add the booked period as a separate event
echo "{ id: " . $row['id'] . ", title: 'Prenotata', start: '$start', end: '$end', color: 'red', rendering: 'background', className: 'booked-event' },";
// Add the availability period as a separate event starting from the next day
echo "{ id: 'availability-" . $row['id'] . "', title: 'Available', start: moment('$end').add(1, 'day'), color: 'red', rendering: 'background', className: 'available-event' },";
// Add the event for the booked period as a foreground event with the checkout time
echo "{ id: " . $row['id'] . ", title: 'Booked until $checkoutTime', start: '$start', end: '$end', color: 'red', rendering: 'inverse-background', className: 'booked-event' },";

    }
    ?>
]
                                    });
                                });
                            </script>
                        </head>
                        <body>
                        <center><h1>Disponibilità</h1></center>
                        <div>
                        <a href="login.php" class="btn btn-green">Login</a>
                       </div>
                       <div id='calendar'></div>
                      </body>
                      </html>
                        


