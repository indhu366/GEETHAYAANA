<?php
session_start();

$_SESSION['isSuperAdmin'] = false; // ✅ RESET FIRST

include "db.php";

/* 🔥 CHECK PORTAL STATUS */
$res = $conn->query("SELECT portal_status FROM settings LIMIT 1");
$row = $res->fetch_assoc();

/* ✅ ALLOW SUPER ADMIN ALWAYS */
if($row['portal_status'] == "closed" && $_POST['email'] != "superadmin@gsss.edu.in"){
    header("Location: /GEETHAYAANA/frontend-clean/studentside/login.html?closed=1");
    exit();
}



$email = $_POST['email'];
$password = $_POST['password'];

/* 🔥 SUPER ADMIN */
$superEmail = "superadmin@gsss.edu.in";
$superPassword = "geethayana2026";

if($email === $superEmail && $password === $superPassword){
    $_SESSION['adminEmail'] = $email;
    $_SESSION['isSuperAdmin'] = true;

    header("Location: ../frontend-clean/adminside/admindashboard.php");
    exit();
}

$superEmail = "superadmin@gsss.edu.in";

if($email == $superEmail){
    $_SESSION['isSuperAdmin'] = true;
}else{
    $_SESSION['isSuperAdmin'] = false;
}

/* NORMAL USERS */
$sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {

    $row = $result->fetch_assoc();

    if ($row['role'] == "admin") {

        if(!preg_match("/@gsss\.edu\.in$/", $row['email'])){
            die("❌ Only @gsss.edu.in emails allowed for admin");
        }

        $_SESSION['adminEmail'] = $row['email'];
        $_SESSION['role'] = "faculty";
        $_SESSION['isSuperAdmin'] = false; // ✅ ensure normal admin

        header("Location: ../frontend-clean/adminside/admindashboard.php");
        exit();

    } else {

        $_SESSION['userEmail'] = $row['email'];
        $_SESSION['role'] = "student";

        header("Location: ../frontend-clean/studentside/studentdashboard.php");
        exit();
    }

} else {
    echo "Invalid email or password";
}
?>