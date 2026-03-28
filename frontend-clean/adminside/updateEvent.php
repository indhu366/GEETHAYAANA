<?php
include "../../backend/db.php";

/* GET DATA FROM FORM */
$id = $_POST['id'];
$title = $_POST['title'];
$date = $_POST['date'];
$time = $_POST['time'];
$venue = $_POST['venue'];

/* 🔍 GET OLD DATA (IMPORTANT) */
$old = $conn->query("SELECT * FROM events WHERE id='$id'");
$oldData = $old->fetch_assoc();

$oldVenue = $oldData['venue'];

/* ✅ UPDATE EVENT */
$conn->query("
    UPDATE events 
    SET title='$title', date='$date', time='$time', venue='$venue'
    WHERE id='$id'
");

$changes = "";

if($oldData['title'] != $title){
    $changes .= "<p>🔹 Title: {$oldData['title']} → $title</p>";
}

if($oldData['date'] != $date){
    $changes .= "<p>🔹 Date: {$oldData['date']} → $date</p>";
}

if($oldData['time'] != $time){
    $changes .= "<p>🔹 Time: {$oldData['time']} → $time</p>";
}

if($oldData['venue'] != $venue){
    $changes .= "<p>🔹 Venue: {$oldData['venue']} → $venue</p>";
}

/* 🔥 CHECK: VENUE CHANGED OR NOT */
if(
    $oldData['title'] != $title ||
    $oldData['date'] != $date ||
    $oldData['time'] != $time ||
    $oldData['venue'] != $venue
){

    include "../../backend/sendEmail.php"; // 🔥 email function

    $eventId = $id;

    /* GET PARTICIPANTS EMAIL */
    $res = $conn->query("
        SELECT email FROM registrations 
        WHERE eventId='$eventId'
    ");

    while($row = $res->fetch_assoc()){

        $to = $row['email'];

        $subject = "⚠️ Venue Changed - Important Update";

        $message = "
<h3>📢 Geethayaana2026 Notification</h3>

<p><b>Event:</b> $title</p>

<h4 style='color:red;'>⚠️ Changes Made:</h4>

$changes

<br>
<p><b>Updated Details:</b></p>
<p>Date: $date</p>
<p>Time: $time</p>
<p>Venue: $venue</p>

<br>
<p style='color:red;'><b>Please note these changes.</b></p>
";

       if(sendEmail($to, $subject, $message)){
    echo "✅ Sent to $to <br>";
} else {
    echo "❌ Failed to $to <br>";
}
    }
    sleep(1); 

}

/* REDIRECT BACK */
echo "<script>alert('Event updated & notifications sent'); window.location.href='../adminside/eventlist.php';</script>";
exit();
?>