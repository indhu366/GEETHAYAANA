<?php
include("../../backend/db.php");

 

if ($conn->connect_error) {
    die("Connection failed");
}

if (!isset($_GET['id'])) {
    die("No event selected");
}

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM events WHERE id='$id'");
$event = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Event Details </title>
<link rel="stylesheet" href="../common/css/style.css">
</head>

<body>

<div class="topbar">
  <span class="menu-btn" onclick="toggleSidebar()">☰</span>

  <h2 class="logo">GEETHAYAANA 2026</h2>
</div>
<div class="sidebar" id="sidebar">
  <a href="studentdashboard.php">🏠 Dashboard</a>
  <a href="events.php">🎯 Events</a>
  <a href="gallery.html">🖼️ Gallery</a>
  <a href="myevents.php">📅 My Events</a>
</div>

<section class="container" style="padding:30px 0;">

<a href="events.php" class="btn outline" style="margin-bottom:18px;">
⬅ Back to Events
</a>

<div class="card" style="padding:26px;">

<!-- IMAGE -->
<?php if(!empty($event['image'])){ ?>
<img 
src="../backend/uploads/<?php echo $event['image']; ?>"
style="width:100%;border-radius:10px;margin-bottom:20px;max-height:400px;object-fit:cover;"
>
<?php } ?>

<div style="display:flex;justify-content:space-between;flex-wrap:wrap;">

<div>
<h1 style="font-size:34px;margin-bottom:10px;">
🎯 <?php echo $event['title']; ?>
</h1>

<span style="padding:6px 12px;border-radius:999px;font-weight:700;
background:rgba(249,115,22,0.15);color:orange;border:1px solid rgba(249,115,22,0.35);">
<?php echo $event['type']; ?>
</span>
</div>

<div style="text-align:right;">
<p style="color:gray;">Event Status</p>
<p style="font-weight:800;color:#22c55e;">
<?php echo $event['status']; ?>
</p>
</div>

</div>

<hr style="margin:18px 0;">

<!-- INFO -->
<div class="grid" style="grid-template-columns:repeat(4,1fr);gap:14px;">

<div class="card"><h3>📅 Date</h3><p><?php echo $event['date']; ?></p></div>
<div class="card"><h3>⏰ Time</h3><p><?php echo $event['time']; ?></p></div>
<div class="card"><h3>📍 Venue</h3><p><?php echo $event['venue']; ?></p></div>

<div class="card">
<h3>👥 Team Size</h3>
<p>
<?php
if($event['participationType']=="team"){
    echo $event['minTeamSize']." - ".$event['teamSize'];
}else{
    echo "Individual";
}
?>
</p>
</div>

</div>

<!-- DESCRIPTION -->
<div style="margin-top:18px;">
<h2>About this Event</h2>
<p style="color:gray;"><?php echo $event['description']; ?></p>
</div>

<!-- DOCUMENT -->
<div style="margin-top:20px;">
<?php if(!empty($event['file'])){ ?>
<a href="../../backend/uploads/<?php echo $event['file']; ?>" target="_blank" class="btn orange">
📄 View / Download Event Document
</a>
<?php } ?>
</div>

<!-- FACULTY -->
<div style="margin-top:20px;">
<h2>Faculty Coordinators</h2>

<?php
$faculty = explode(",", $event['faculty'] ?? "");

if(empty($faculty)){
    echo "<li>No faculty coordinators listed</li>";
}else{
    foreach($faculty as $f){
        if(trim($f) != ""){
            echo "<li>$f</li>";
        }
    }
}
?>

<h2 style="margin-top:15px;">Student Coordinators</h2>

<?php
$students = explode(",", $event['students'] ?? "");

if(empty($students)){
    echo "<li>No student coordinators listed</li>";
}else{
    foreach($students as $s){
         if(trim($s) != ""){
            echo "<li>$s</li>";
        }
    }
}
?>

</div>

<?php if(isset($_GET['joined'])){ ?>
    <a href="<?php echo $event['whatsappLink']; ?>" target="_blank">
        👉 Join WhatsApp Group
    </a>
<?php } ?>

<!-- REGISTER BUTTON -->
<div style="margin-top:22px;">
<a href="registerforevent.php?id=<?php echo $event['id']; ?>" class="btn orange">
Register Now
</a>
</div>

</div>
</section>

<script>
    function toggleSidebar(){
  let sidebar = document.getElementById("sidebar");
  sidebar.style.left = (sidebar.style.left === "0px") ? "-220px" : "0px";
}

function toggleProfile(){
  let box = document.getElementById("profileBox");
  box.style.display = (box.style.display === "block") ? "none" : "block";
}
</script>


<footer class="footer">
<div class="container">
© 2026 GeethsConnect | Event Details
</div>
</footer>

</body>
</html>