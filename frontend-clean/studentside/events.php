<?php
include("../../backend/db.php");

if ($conn->connect_error) {
    die("DB Connection Failed");
}

$result = $conn->query("SELECT * FROM events");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Events | GEETHAYAANA2026</title>
<link rel="stylesheet" href="../common/css/style.css" />

<style>
#searchInput {
  width: 60% !important;
  max-width: 800px;
  display: block;
  margin: 0 auto 20px auto;
}

#searchInput:focus {
  border-color: #ff7a00;
  box-shadow: 0 0 5px rgba(255, 122, 0, 0.5);
}

#searchInput::placeholder {
  color: #888;
}

.logo{
  position:absolute;
  left:50%;
  transform:translateX(-50%);
  font-size:20px;
  font-weight:bold;
  color:#ff7a00;
}

.topbar{
  display:flex;
  align-items:center;
  justify-content:space-between;
  position:relative; /* IMPORTANT */
  background:#111;
  color:#fff;
  padding:12px 20px;
}

.menu-btn{
  font-size:24px;
  cursor:pointer;
}

/* SIDEBAR */
.sidebar{
  position:fixed;
  top:0;
  left:-220px;
  width:200px;
  height:100%;
  background:#222;
  padding-top:60px;
  transition:0.3s;
  z-index:1000;
}

.sidebar a{
  display:block;
  color:#fff;
  padding:12px 20px;
  text-decoration:none;
}

.sidebar a:hover{
  background:#444;
}

/* PROFILE */
.profile-dropdown{
  display:none;
  position:absolute;
  right:0;
  top:45px;
  background:#ffffff;
  color:#111;
  padding:15px;
  border-radius:12px;
  width:220px;
  box-shadow:0 8px 25px rgba(0,0,0,0.2);
  z-index:1000;
  animation:fadeIn 0.2s ease;
}

.profile-dropdown p{
  margin:5px 0;
  font-size:14px;
}

.profile-dropdown strong{
  font-size:16px;
}

.profile-dropdown a{
  display:block;
  padding:10px;
  margin-top:5px;
  border-radius:8px;
  text-decoration:none;
  color:#111;
  transition:0.2s;
}

.profile-dropdown a:hover{
  background:#f2f2f2;
}

@keyframes fadeIn{
  from{opacity:0; transform:translateY(-10px);}
  to{opacity:1; transform:translateY(0);}
}
</style>
</head>
<body>

<!-- Navbar -->
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
<div class="card">
<h1 style="font-size:30px;margin-bottom:8px;">🎉 Events</h1>
<p style="color:var(--muted);">All events published by Faculty will appear here.</p>
</div>
</section>

<input type="text" id="searchInput" placeholder="Search events..."
style="width:100%;padding:10px;margin-bottom:20px;">

<section class="container" style="margin-bottom:40px;">
<div id="eventList" class="grid" style="grid-template-columns:repeat(2,1fr);gap:14px;">

<?php
if($result->num_rows == 0){
    echo "<p>No events available</p>";
}else{

    while($row = $result->fetch_assoc()){

        /* COUNT REGISTERED */
        $countRes = $conn->query("SELECT COUNT(DISTINCT teamId) as total FROM registrations WHERE eventId='".$row['id']."'");
        $countRow = $countRes->fetch_assoc();
        $registered = $countRow['total'];

       echo '
<div class="card" style="padding:18px">

<h2 style="margin-bottom:6px;">'.$row['title'].'</h2>

<p style="color:var(--muted);margin:0;">
'.$row['type'].' • '.$row['date'].' • '.$row['time'].'
</p>

<p style="color:var(--muted);margin-top:6px;">
📍 '.$row['venue'].'
</p>

<p style="margin-top:10px;color:var(--muted);">
'.(isset($row['description']) ? substr($row['description'],0,100) : '').'...
</p>

<button class="btn orange" style="margin-top:12px;"
onclick="window.location.href=\'eventdetails.php?id='.$row['id'].'\'">
View Details
</button>

</div>
';
    }
}
?>

</div>
</section>

<script>
document.getElementById("searchInput").addEventListener("keyup", function() {
    let value = this.value.toLowerCase();
    let cards = document.querySelectorAll("#eventList .card");

    cards.forEach(card => {
        let text = card.innerText.toLowerCase();

        if(text.includes(value)){
            card.style.display = "";
        } else {
            card.style.display = "none";
        }
    });
});


function toggleSidebar(){
  let sidebar = document.getElementById("sidebar");
  sidebar.style.left = (sidebar.style.left === "0px") ? "-220px" : "0px";
}

</script>

<footer class="footer">
<div class="container">© 2026 GEETHAYAANA | Events</div>
</footer>

</body>
</html>