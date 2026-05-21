<?php
session_start();
include "db.php";

/* ✅ CHECK STATUS */
if(isset($_GET['check'])){
    $res = $conn->query("SELECT portal_status FROM settings LIMIT 1");
    $row = $res->fetch_assoc();

    echo json_encode([
        "status" => $row['portal_status']
    ]);
    exit();
}

if(!isset($_SESSION['isSuperAdmin']) || $_SESSION['isSuperAdmin'] != true){
    die("Access denied");
}

$action = $_POST['action'];

$status = ($action == "open") ? "open" : "closed";

$conn->query("UPDATE settings SET portal_status='$status' WHERE id=1");

/* ✅ SHOW MESSAGE */
if($status == "closed"){
    echo "<script>
    alert('🚫 Portal is now CLOSED');
    window.location.href='../frontend-clean/adminside/admindashboard.php';
    </script>";
} else {
    echo "<script>
    alert('🟢 Portal is now OPEN');
    window.location.href='../frontend-clean/adminside/admindashboard.php';
    </script>";
}
exit;
?>