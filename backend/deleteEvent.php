<?php
session_start();
$loggedAdmin = $_SESSION['adminEmail'];

$conn = new mysqli("localhost", "root", "", "geethayana",3307);

$id = $_GET['id'];

$check = $conn->query("SELECT * FROM events WHERE id='$id'");
$row = $check->fetch_assoc();

/* 🔐 SECURITY CHECK */
if($row['createdBy'] != $loggedAdmin){
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