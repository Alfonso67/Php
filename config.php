<?php
// Database connection settings
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'reservation';

// Create connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if($conn === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
