<?php
$conn = new mysqli("localhost", "root", "", "geethayana");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$eventId = $_GET['eventId'] ?? '';

$result = $conn->query("SELECT * FROM registrations WHERE eventId='$eventId'");

$data = [];

while($row = $result->fetch_assoc()){
    $data[] = $row;
}

echo json_encode($data);
?>