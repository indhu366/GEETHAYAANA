<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include __DIR__ . "/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role']; // should be "admin" for faculty
    $empId = NULL;

if($role == "admin"){
    if(empty($_POST['empId'])){
        die("❌ Employee ID required for faculty");
    }
    $empId = "E" . $_POST['empId'];
}

    if (!$name || !$email || !$password || !$role) {
        die("Fill all fields");
    }

    if($role == "admin"){
        if(substr($email, -12) !== "@gsss.edu.in"){
            echo "<script>
alert('Invalid Email for faculty.');
window.history.back();
</script>";
exit();
            
        }
    }

    $check = $conn->query("SELECT * FROM users WHERE email='$email'");

    if($check->num_rows > 0){
        die("Email already registered");
    }

   $sql = "INSERT INTO users (name, email, password, role, empId)
        VALUES ('$name', '$email', '$password', '$role', " . ($empId ? "'$empId'" : "NULL") . ")
        ";
        if ($conn->query($sql)) {header("Location: /GEETHAYAANA/frontend-clean/studentside/login.html");
        exit();
    } else {
        die("SQL ERROR: " . $conn->error); // 🔥 THIS LINE WILL SHOW REAL ERROR
    }
}
?>