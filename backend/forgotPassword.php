<?php
include "db.php";

$email = $_POST['email'];
$newPassword = $_POST['newPassword'];

/* CHECK EMAIL EXISTS */
$check = $conn->query("SELECT * FROM users WHERE email='$email'");

if($check->num_rows > 0){

    /* UPDATE PASSWORD */
    $conn->query("UPDATE users SET password='$newPassword' WHERE email='$email'");

    header("Location: /GEETHAYAANA/frontend-clean/studentside/login.html");
    exit();

} else {
    echo "❌ Email not found";
}
?>