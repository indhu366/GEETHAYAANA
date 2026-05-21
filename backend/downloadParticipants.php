<?php
include("db.php");

$eventId = $_GET['eventId'];

$result = $conn->query("SELECT * FROM registrations WHERE eventId='$eventId'");

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="participants.csv"');

$output = fopen("php://output", "w");

fputcsv($output, ["Name","USN","Phone","Department"]);

while($row = $result->fetch_assoc()){
    fputcsv($output, [
        $row['name'],
        $row['usn'],
        $row['phone'],
        $row['department']
    ]);
}

fclose($output);
?>