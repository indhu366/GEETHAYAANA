<?php
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (!$name || !$email || !$password || !$role) {
        echo "Fill all fields";
        exit();
    }
    $check = $conn->query("SELECT * FROM users WHERE email='$email'");

if($check->num_rows > 0){
    echo "❌ Email already registered";
    exit();
}

    $sql = "INSERT INTO users (name, email, password, role)
            VALUES ('$name', '$email', '$password', '$role')";

    if ($conn->query($sql)) {
        header("Location: GEETHAYAANA/frontend-clean/studentside/login.html");
        exit();
    } else {
        echo "Error";
    }
}
?>