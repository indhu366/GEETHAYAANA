<?php
session_start();

$isSuperAdmin = isset($_SESSION['isSuperAdmin']);
$email = $_SESSION['adminEmail'];
include("../../backend/db.php");



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

<div class="topbar">


  <h2 class="logo-text">GEETHAYAANA 2026</h2>
  <a href="eventlist.php">Back</a>

</div>

<div class="container" style="padding:40px;">
<div class="card">

<h2>✏️ Edit Event</h2>

<form method="POST" action="../../backend/updateEvent.php" enctype="multipart/form-data">

<input type="hidden" name="id" value="<?php echo $event['id']; ?>">

<label>Title</label>
<input type="text" name="title" value="<?php echo $event['title']; ?>" required>

<label>Date</label>
<input type="date" name="date" value="<?php echo $event['date']; ?>" required>

<label>Time</label>
<input type="time" name="time" value="<?php echo $event['time']; ?>" required>

<label>Venue</label>
<input type="text" name="venue" value="<?php echo $event['venue']; ?>" required>

<label>WhatsApp Group Link</label>
<input type="url" name="whatsappLink" 
value="<?php echo $event['whatsappLink']; ?>">

<label>Event Status</label>
<select name="status" required>
    <option value="Open" <?php if($event['status']=="Open") echo "selected"; ?>>Open</option>
    <option value="Closed" <?php if($event['status']=="Closed") echo "selected"; ?>>Closed</option>
</select>

<label>Upload New File (optional)</label>
<input type="file" name="image" accept="image/*,.pdf">
 


<!-- KEEP OLD FILE -->
<input type="hidden" name="oldFile" 
value="<?php echo $event['file']; ?>">

<br><br>

<button type="submit" class="btn">Update Event</button>

</form>

</div>
</div>

</body>
</html>