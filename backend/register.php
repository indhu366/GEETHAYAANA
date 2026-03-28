<?php
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
    die("❌ Event not found");
}

$min = $event['minTeamSize'];
$max = $event['teamSize'];

/* VALIDATION */
if($type == "team"){
    if($count < $min || $count > $max){
        die("❌ Invalid team size");
    }
}else{
    if($count != 1){
        die("❌ Only 1 participant allowed");
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
        die("❌ Event full");
    }
}else{
    if($current >= $event['maxTeams']){
        die("❌ Team slots full");
    }
}

/* TEAM ID */
$teamId = ($type == "team") ? uniqid("TEAM_") : NULL;

/* INSERT LOOP */
for($i=1; $i<=$count; $i++){

    $name = $_POST["name$i"] ?? "";
    $usn = $_POST["usn$i"] ?? "";
    $phone = $_POST["phone$i"] ?? "";
    $email = $_POST["email$i"] ?? "";
    $dept = $_POST["dept$i"] ?? "";

    if(empty($name) || empty($usn)){
        die("❌ Fill all required fields");
    }

    /* DUPLICATE CHECK */
    $check = $conn->query("SELECT * FROM registrations 
        WHERE eventId='$eventId' AND usn='$usn'");

    if($check->num_rows > 0){
        die("❌ $usn already registered");
    }

    $sql = "INSERT INTO registrations
    (eventId,eventTitle,eventDate,eventTime,name,usn,phone,email,department,teamId)
    VALUES
    ('$eventId','$title','$date','$time','$name','$usn','$phone','$email','$dept','$teamId')";

    if(!$conn->query($sql)){
        die("DB Error: ".$conn->error);
    }
}

/* SUCCESS */
echo "<script>
alert('✅ Registration Successful');
window.location.href='../frontend-clean/studentside/events.php';
</script>";
?>