<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "geethayana",3307);

if ($conn->connect_error) {
    echo json_encode(["message" => "DB Connection Failed"]);
    exit();
}

$result = $conn->query("SELECT * FROM events");

$events = [];

while($row = $result->fetch_assoc()){

    /* ✅ COUNT REGISTERED STUDENTS */
    $countRes = $conn->query("SELECT COUNT(*) as total FROM registrations WHERE eventId='".$row['id']."'");
    $countRow = $countRes->fetch_assoc();

    $row['registeredCount'] = $countRow['total'];

    $events[] = $row;
}
echo json_encode($events);
?>