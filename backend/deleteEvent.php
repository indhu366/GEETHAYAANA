<?php
session_start();
include("db.php");

$isSuperAdmin = $_SESSION['isSuperAdmin'] ?? false;
$loggedAdmin = $_SESSION['adminEmail'] ?? '';



$id = $_GET['id'];

/* GET EVENT */
$check = $conn->query("SELECT createdBy FROM events WHERE id='$id'");
$row = $check->fetch_assoc();

/* 🔐 SECURITY CHECK */
if(!$isSuperAdmin && $row['createdBy'] != $loggedAdmin){
    echo json_encode(["status"=>"error","message"=>"Not authorized"]);
    exit();
}

/* 🔥 DELETE RELATED REGISTRATIONS */
$conn->query("DELETE FROM registrations WHERE eventId='$id'");

/* 🔥 DELETE EVENT */
$conn->query("DELETE FROM events WHERE id='$id'");

/* RESPONSE */
echo json_encode(["status"=>"success","message"=>"Event deleted"]);
?>