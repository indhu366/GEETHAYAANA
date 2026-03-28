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
$title = $_POST['title'];
$type = $_POST['type'];
$date = $_POST['date'];
$time = $_POST['time'];
$venue = $_POST['venue'];
$status = $_POST['status'];
$description = $_POST['description'];
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
            $studentData .= $_POST['studentName'][$i]." - ".$_POST['studentUSN'][$i].", ";
        }
    }
}

/* FILE */
$fileName = "";
if(isset($_FILES['eventFile']) && $_FILES['eventFile']['name'] != ""){
    $fileName = time()."_".$_FILES['eventFile']['name'];
    move_uploaded_file($_FILES['eventFile']['tmp_name'], "uploads/".$fileName);
}

/* INSERT */
$stmt = $conn->prepare("INSERT INTO events
(title,type,date,time,venue,status,description,participationType,teamSize,minTeamSize,maxParticipants,maxTeams,faculty,students,file,createdBy)
VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

if(!$stmt){
    echo json_encode(["status"=>"error","message"=>$conn->error]);
    exit;
}

$stmt->bind_param(
    "ssssssssiiiissss",
    $title,
    $type,
    $date,
    $time,
    $venue,
    $status,
    $description,
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