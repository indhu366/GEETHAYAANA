<?php
session_start(); // ✅ ONLY ONCE

include "db.php";

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {

    $row = $result->fetch_assoc();

    // 🔥 CHECK ROLE
    if ($row['role'] == "admin") {

        $_SESSION['adminEmail'] = $row['email']; // ✅ STORE EMAIL

        header("Location: /GEETHAYAANA/frontend-clean/adminside/admindashboard.php");
        exit();

    } else {

        $_SESSION['userEmail'] = $row['email']; // (optional but good)

        header("Location: /GEETHAYAANA/frontend-clean/studentside/studentdashboard.php");
        exit();
    }

} else {
    echo "Invalid email or password";
}
?>