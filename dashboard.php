<?php
// Initialize the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pannello di Amministrazione</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet" href="http://localhost/room_res/css/dashboard.css">
</head>
<body>
    <div class="wrapper">
        <div class="header">
        <h1>Pannello di Amminstrazione</h1>
        <div class="user-info">
            <span>Benvenuto, <?php echo htmlspecialchars($_SESSION["username"]); ?></span>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="dashboard">
        <div class="sidebar">
            <ul>
                <li><a href="calendar.php">Calendario prenotazioni</a></li>
                <li><a href="view_bookings.php">Visualizza tutte le prenotazioni</a></li>
                <li><a href="create_booking.php">Registra prenotazione</a></li>
                <li><a href="search_bookings.php">Verifica disponibilità</a></li>
                <li><a href="registration.php">Crea Account utente</a></li>
                <li><a href="manage_users.php">Gestione utenti</a></li>
            </ul>
        </div>

        <div class="content">
            <div class="main-content">
                <div class="page">
                    <h2>Benvenuto nel Pannello di Amministrazione</h2>
<p style="text-align: justify;">Questo cruscotto ti consente di gestire varie funzioni e impostazioni del sistema di prenotazione.</p>
<p style="text-align: justify;">Le funzioni disponibili nel pannello di amministrazione includono:</p>
<ul style="text-align: justify;">
    <li>Gestione del calendario delle prenotazioni: puoi visualizzare e gestire le prenotazioni effettuate.</li>
    <li>Visualizzazione di tutte le prenotazioni: puoi vedere un elenco completo di tutte le prenotazioni effettuate.</li>
    <li>Registrazione di una nuova prenotazione: puoi aggiungere manualmente una nuova prenotazione nel sistema.</li>
    <li>Verifica della disponibilità: puoi controllare la disponibilità delle stanze per un determinato periodo di tempo.</li>
    <li>Creazione di account utente: puoi creare nuovi account utente per consentire l'accesso al sistema.</li>
    <li>Gestione degli utenti: puoi visualizzare e gestire gli account utente esistenti.</li>
</ul>
                </div>
            </div>
        </div>
    </div>
     <br>
     <br>
     <br>
     <br>
     <br>
     <br>
     <br>
     <br>
     <br>
     <br>
     <br>
     <br>
    <div class="footer">
       <center>&copy; <?php echo date("Y"); ?> Name of your Company</center>
    </div>
</body>
</html>
