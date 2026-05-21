<?php
include("db.php");

$id = $_GET['id'];

$result = $conn->query("SELECT * FROM events WHERE id='$id'");
$data = $result->fetch_assoc();

echo json_encode($data);
?>