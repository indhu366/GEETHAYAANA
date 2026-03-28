<?php
include "db.php";

$eventId = $_GET['eventId'];

/* GET EVENT */
$eventQuery = mysqli_query($conn, "SELECT * FROM events WHERE id='$eventId'");
$event = mysqli_fetch_assoc($eventQuery);

/* TEAM EVENT */
if($event['teamSize'] > 1){

    $teams = mysqli_query($conn, "SELECT COUNT(*) as total FROM registrations 
    WHERE eventId='$eventId' AND role='Team Leader'");

    $teamData = mysqli_fetch_assoc($teams);

    $max = $event['maxTeams'];
    $remaining = $max - $teamData['total'];

}else{

    /* INDIVIDUAL EVENT */
    $count = mysqli_query($conn, "SELECT COUNT(*) as total FROM registrations 
    WHERE eventId='$eventId'");

    $data = mysqli_fetch_assoc($count);

    $max = $event['maxParticipants'];
    $remaining = $max - $data['total'];
}

echo json_encode([
    "remaining" => max(0, $remaining)
]);
?>