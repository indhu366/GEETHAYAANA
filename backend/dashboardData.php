<?php
session_start();
include("db.php");

// TOTAL EVENTS
$events = mysqli_query($conn, "SELECT COUNT(*) as total FROM events");
$eventData = mysqli_fetch_assoc($events);

// TOTAL PARTICIPANTS
$participants = mysqli_query($conn, "SELECT COUNT(*) as total FROM registrations");
$participantData = mysqli_fetch_assoc($participants);

// DEPARTMENT COUNT
$dept = mysqli_query($conn, "
SELECT department, COUNT(*) as count 
FROM registrations 
GROUP BY department
");

$deptData = [];

while($row = mysqli_fetch_assoc($dept)){
    $deptData[] = $row;
}

echo json_encode([
    "events" => $eventData['total'],
    "participants" => $participantData['total'],
    "departments" => $deptData
]);
?>