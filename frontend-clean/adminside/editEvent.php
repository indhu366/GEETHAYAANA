<?php
session_start();
include "../../backend/db.php";



$loggedAdmin = $_SESSION['adminEmail']; // or whatever you use

$id = $_GET['id'];

$result = $conn->query("SELECT * FROM events WHERE id='$id'");
$event = $result->fetch_assoc();

/* ❌ BLOCK UNAUTHORIZED ACCESS */
if($event['createdBy'] != $loggedAdmin){
    die("❌ You are not allowed to edit this event");
}

/* GET EXISTING EVENT DATA */
$result = $conn->query("SELECT * FROM events WHERE id='$id'");
$event = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Event</title>
<link rel="stylesheet" href="../common/css/style.css">
</head>

<body>

<div class="container" style="padding:40px;">
<div class="card">

<h2>✏️ Edit Event</h2>

<form method="POST" action="updateEvent.php">

<input type="hidden" name="id" value="<?php echo $event['id']; ?>">

<label>Title</label>
<input type="text" name="title" value="<?php echo $event['title']; ?>" required>

<label>Date</label>
<input type="date" name="date" value="<?php echo $event['date']; ?>" required>

<label>Time</label>
<input type="time" name="time" value="<?php echo $event['time']; ?>" required>

<label>Venue</label>
<input type="text" name="venue" value="<?php echo $event['venue']; ?>" required>

<br><br>

<button type="submit" class="btn">Update Event</button>

</form>

</div>
</div>

</body>
</html>