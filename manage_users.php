<?php
session_start();
// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit;
}
include('config.php');

// Create connection
//$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query the database
$sql = "SELECT * FROM admins";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Gestione Utenti</title>
    <link rel="stylesheet" href="http://localhost/room_res/css/viewbooking.css">
</head>
<style>
    body {
    max-width: 70%;
    margin: 0 auto;
    }
    </style>
<body>
<h1 style="display: inline-block;">Utenti presenti</h1>
<a href="dashboard.php" style="position: relative; top: 52px; left: 1030px; background-color: green; color: white; padding: 8px; text-decoration: none;">Torna Indietro</a>
    <!--<h2>Utenti presenti</h2>//-->
    <?php if ($result->num_rows > 0) : ?>
        <table>
            <tr>
                <th>Nome utente</th>
                <th>Password</th>
                <th>Azione</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?php echo $row["username"]; ?></td>
                    <td><?php echo $row["password"]; ?></td>
                    <td>
                        <a href="manage_users.php?action=edit&id=<?php echo $row["id"]; ?>"><button type="submit">Modifica</button></a>
                        <a href="manage_users.php?action=delete&id=<?php echo $row["id"]; ?>" onclick="return confirm('L\'utente verrà eliminato, sei sicuro?');"><button type="submit" style="background-color: red;">Cancella</button></a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else : ?>
        <p>Nessun utente trovato.</p>
    <?php endif; ?>
    <?php
    // Handle edit or delete action
    if (isset($_GET["action"]) && isset($_GET["id"])) {
        $action = $_GET["action"];
        $id = $_GET["id"];

        if ($action === "edit") {
            // Retrieve user from the database
            $stmt = $conn->prepare("SELECT * FROM admins WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            // Check if user exists
            if (!$user) {
                echo "User not found.";
                exit();
            }

            // Handle form submission
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                $username = $_POST["username"];
                $password = $_POST["password"];

                // Hash the password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                //new code for verify if username is present
                // Check if the updated username already exists
                $stmt = $conn->prepare("SELECT * FROM admins WHERE username=? AND id<>?");
                $stmt->bind_param("si", $username, $id);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                // Username already exists
                echo '<script>alert("Nome utente esistente."); window.location.href = "manage_users.php?action=edit&id='.$id.'";</script>';
             // You can also redirect back to the same page
             header("Location: manage_users.php?action=edit&id=".$id);
             exit();
           }
                //end code
                // Update user in the database
                $stmt = $conn->prepare("UPDATE admins SET username=?, password=? WHERE id=?");
                $stmt->bind_param("ssi", $username, $hashedPassword, $id);
                $stmt->execute();

                // Redirect to manage_users.php after the update
                header("Location: manage_users.php");
                exit();
            }
        } elseif ($action === "delete") {
            // Delete user from the database
            $stmt = $conn->prepare("DELETE FROM admins WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();

            // Redirect to manage_users.php after the deletion
            header("Location: manage_users.php");
            exit();
        }
    }
    ?>

<?php if (isset($user)) : ?>
    <h2 class="title">Modifica utente</h2>
    <style>
    form {
      background-color: #f0f0f0; /* Adjust the color value as needed */
      padding: 20px;
      border-radius: 4px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.5); /* Adjust the shadow values as needed */
    }
    h2 {
      color: red;
    }
  </style>
    <form method="POST" action="manage_users.php?action=edit&id=<?php echo $user['id']; ?>" class="user-form">
    <label for="username">Username:</label>
    <input type="text" name="username" value="<?php echo $user['username']; ?>"><br>
    <br>
    <label for="password">Password:</label>
    <input type="password" name="password" value=""><br>
    <br>
    <input type="submit" value="Aggiorna">
    <button type="button" onclick="window.location.href='manage_users.php';">Cancella</button>
    </form>
   <?php endif; ?>
    </body>
    </html>
<?php
// Close the database connection
$conn->close();
?>


