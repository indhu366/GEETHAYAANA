<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "db.php";


$eventId = $_POST['eventId'];
$title = $_POST['eventTitle'];
$date = $_POST['eventDate'];
$time = $_POST['eventTime'];
$count = $_POST['count'];
$type = $_POST['participationType'];

/* GET EVENT */
$event = $conn->query("SELECT * FROM events WHERE id='$eventId'")->fetch_assoc();


if(!$event){
    echo json_encode(["status"=>"error","message"=>"Event not found"]);
exit;
    
}

/* 🚫 BLOCK IF EVENT CLOSED */
if($event['status'] == "Closed"){
    echo json_encode([
        "status"=>"error",
        "message"=>"Registration is closed for this event"
    ]);
    exit;
}

$min = $event['minTeamSize'];
$max = $event['teamSize'];

/* VALIDATION */
if($type == "team"){
    if($count < $min || $count > $max){
        
echo json_encode(["status"=>"error","message"=>"Invalid team size"]); exit;

    }
}else{
    if($count != 1){
        echo json_encode(["status"=>"error","message"=>"Only 1 participant allowed"]); exit;
    }
}

/* CHECK LIMIT */
if($type == "team"){
    $countRes = $conn->query("SELECT COUNT(DISTINCT teamId) as total FROM registrations WHERE eventId='$eventId'");
}else{
    $countRes = $conn->query("SELECT COUNT(*) as total FROM registrations WHERE eventId='$eventId'");
}

$row = $countRes->fetch_assoc();
$current = $row['total'];

if($type == "individual"){
    if($current >= $event['maxParticipants']){
        echo json_encode(["status"=>"error","message"=>"Event full"]); exit;
    }
}else{
    if($current >= $event['maxTeams']){
        echo json_encode(["status"=>"error","message"=>"Team slots full"]); exit;
    }
}

/* 🔥 CHECK ALL MEMBERS FIRST */
for($i=1; $i<=$count; $i++){

    $usn = $_POST["usn$i"] ?? "";

    if(empty($usn)){
        echo json_encode(["status"=>"error","message"=>"USN missing for member $i"]);
        exit;
    }

    $check = $conn->query("SELECT * FROM registrations 
        WHERE eventId='$eventId' AND usn='$usn'");

    if($check->num_rows > 0){
        echo json_encode(["status"=>"error","message"=>"$usn already registered"]);
        exit;
    }
}

/* TEAM ID */
$teamId = ($type == "team") ? uniqid("TEAM_") : NULL;

/* INSERT LOOP */
for($i=1; $i<=$count; $i++){

    $name = $_POST["name$i"] ?? "";
    $dept = $_POST["dept$i"] ?? "";
    $sem = $_POST['sem'] ?? "";
    $usn = $_POST["usn$i"] ?? "";
    $phone = $_POST["phone$i"] ?? "";
    $email = $_POST["email$i"] ?? "";
    $team_name = $_POST['team_name'] ?? "";

    if(empty($name) || empty($usn)){
        echo json_encode(["status"=>"error","message"=>"Fill all required fields"]); exit;
    }

   

   $sql = "INSERT INTO registrations
(eventId,eventTitle,eventDate,eventTime,name,usn,phone,email,department,sem,team_name,teamId)
VALUES
('$eventId','$title','$date','$time','$name','$usn','$phone','$email','$dept','$sem','$team_name','$teamId')";

    if(!$conn->query($sql)){
        echo json_encode(["status"=>"error","message"=>$conn->error]); exit;
    }

}

/* SUCCESS */
echo json_encode([
    "status" => "success",
    "whatsappLink" => $event['whatsappLink']
]);
exit;
?>