<?php
session_start();
 
// Check if the user is logged in, otherwise redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

require_once "config.php";

// Define variables and initialize with empty values
$start_date = $end_date = "";
$start_date_err = $end_date_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate start date
    if(empty(trim($_POST["start_date"]))){
        $start_date_err = "Please enter a start date.";
    } else{
        $start_date = trim($_POST["start_date"]);
    }
    
    // Validate end date
    if(empty(trim($_POST["end_date"]))){
        $end_date_err = "Please enter an end date.";
    } else{
        $end_date = trim($_POST["end_date"]);
    }
    
    // Check input errors before querying the database
    if(empty($start_date_err) && empty($end_date_err)){
 
        // Prepare a select statement
        $sql = "SELECT * FROM bookings WHERE room_number = ? AND checkin_date BETWEEN ? AND ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "iss", $room_number, $start_date, $end_date);
            
            // Set parameters
            $room_number = $_SESSION["room_number"];
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                $result = mysqli_stmt_get_result($stmt);
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
 
            // Close statement
            mysqli_stmt_close($stmt);
        }
        
        // Close connection
        mysqli_close($link);
    }
}
?>

<?php
// Initialize the session
session_start();

// Unset all of the session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to index page
header("location: index.php");
exit;
?>

