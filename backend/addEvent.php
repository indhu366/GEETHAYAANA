<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");

session_start();

if(!isset($_SESSION['adminEmail'])){
    echo json_encode(["status"=>"error","message"=>"Not logged in"]);
    exit;
}

$loggedAdmin = $_SESSION['adminEmail'];

include "db.php";

/* GET DATA */
if($_POST['title'] == "Other"){
    $title = $_POST['other_event_name'];
} else {
    $title = $_POST['title'];
}
$type = $_POST['type'];
$date = $_POST['date'];
$time = $_POST['time'] . " " . $_POST['am_pm'];
$venue = $_POST['venue'];
$status = $_POST['status'];
$description = $_POST['description'];
$whatsappLink = $_POST['whatsappLink'] ?? "";
$participationType = $_POST['participationType'];

$teamSize = $_POST['teamSize'] ?? 0;
$minTeamSize = $_POST['minTeamSize'] ?? 0;
$maxParticipants = $_POST['maxParticipants'] ?? 0;
$maxTeams = $_POST['maxTeams'] ?? 0;

/* FACULTY */
$facultyData = "";
if(!empty($_POST['facultyName'])){
    for($i=0;$i<count($_POST['facultyName']);$i++){
        if($_POST['facultyName'][$i] != ""){
            $facultyData .= $_POST['facultyName'][$i]." (".$_POST['facultyPhone'][$i]."), ";
        }
    }
}

/* STUDENTS */
$studentData = "";
if(!empty($_POST['studentName'])){
    for($i=0;$i<count($_POST['studentName']);$i++){
        if($_POST['studentName'][$i] != ""){
            $studentData .= $_POST['studentName'][$i]." (".$_POST['studentPhone'][$i]."), ";
        }
    }
}

/* FILE */
$fileName = NULL;

if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){

    $allowed = ['jpg','jpeg','png','gif','pdf'];

    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

    if(!in_array($ext, $allowed)){
        echo json_encode(["status"=>"error","message"=>"Only images and PDF allowed"]);
exit;
    }

    $fileName = time() . "_" . $_FILES['image']['name'];

    move_uploaded_file(
        $_FILES['image']['tmp_name'],
        __DIR__ . "/uploads/" . $fileName
    );
}
/* INSERT */
$stmt = $conn->prepare("INSERT INTO events
(title,type,date,time,venue,status,description,whatsappLink,participationType,teamSize,minTeamSize,maxParticipants,maxTeams,faculty,students,file,createdBy)
VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

if(!$stmt){
    echo json_encode(["status"=>"error","message"=>$conn->error]);
    exit;
}

$stmt->bind_param(
    "sssssssssiiiissss",
    $title,
    $type,
    $date,
    $time,
    $venue,
    $status,
    $description,
    $whatsappLink, 
    $participationType,
    $teamSize,
    $minTeamSize,
    $maxParticipants,
    $maxTeams,
    $facultyData,
    $studentData,
    $fileName,
    $loggedAdmin
);

if($stmt->execute()){
    echo json_encode(["status"=>"success"]);
}else{
    echo json_encode(["status"=>"error","message"=>$stmt->error]);
}

$stmt->close();
$conn->close();
?>