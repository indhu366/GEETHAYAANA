<?php
$conn = new mysqli("localhost", "root", "", "geethayana", 3307);

if ($conn->connect_error) {
    die("DB Error");
}

/* TOTAL EVENTS */
$events = $conn->query("SELECT COUNT(*) as total FROM events");
$eventCount = $events->fetch_assoc()['total'];

/* TOTAL STUDENTS */
$students = $conn->query("SELECT COUNT(*) as total FROM registrations");
$studentCount = $students->fetch_assoc()['total'];

echo json_encode([
    "totalEvents" => (int)$eventCount,
    "totalStudents" => (int)$studentCount
]);
?>