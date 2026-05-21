<?php
session_start();
$isSuperAdmin = $_SESSION['isSuperAdmin'] ?? false;
$email = $_SESSION['adminEmail'];
$loggedAdmin = $_SESSION['adminEmail'];

include("../../backend/db.php");

if ($conn->connect_error) {
    die("Connection failed");
}

$result = $conn->query("SELECT * FROM events");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Events List | GeethsConnect</title>
<link rel="stylesheet" href="../common/css/style.css">

<style>
#eventList{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(300px,1fr));
gap:20px;
}

#searchInput {
    width: 60% !important;
    max-width: 400px;
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
</style>

</head>

<body>

<div class="topbar">


  <h2 class="logo-text">GEETHAYAANA 2026</h2>
  <a href="admindashboard.php">Back</a>

</div>

<section class="container" style="padding:30px 0;">
<div class="card">
<h1>📅 All Events</h1>
<p style="color:var(--muted);">
Events added by admin will appear here.
</p>
</div>
</section>

<input type="text" id="searchInput" placeholder="Search events..."
style="width:100%;padding:10px;margin-bottom:20px;">

<section class="container">
<div id="eventList">

<?php
if($result->num_rows == 0){
    echo "<p>No events added yet.</p>";
}else{
    while($row = $result->fetch_assoc()){

echo '
<div class="card" style="padding:20px;cursor:pointer"
onclick="window.location.href=\'participateslist.php?eventId='.$row['id'].'\'">

<h3>'.$row['title'].'</h3>

<p>'.$row['type'].' | '.$row['date'].' | '.$row['time'].'</p>

<p>📍 '.$row['venue'].'</p>

<div style="margin-top:10px;display:flex;gap:10px;">

<!-- ✅ EDIT BUTTON -->
'.(
($row['createdBy'] == $loggedAdmin || $isSuperAdmin)
? '<a href="editEvent.php?id='.$row['id'].'"
class="btn"
onclick="event.stopPropagation();">
Edit
</a>'
: '<button class="btn"
style="opacity:0.5;cursor:not-allowed;"
onclick="event.stopPropagation();" disabled>
Not Allowed
</button>'
).'

'.(
($row['createdBy'] == $loggedAdmin || $isSuperAdmin)
? '<button class="btn outline"
onclick="deleteEvent('.$row['id'].'); event.stopPropagation();">
Delete Event
</button>'
: '<button class="btn outline"
style="opacity:0.5;cursor:not-allowed;"
onclick="event.stopPropagation();" disabled>
Not Allowed
</button>'
).'

</div>

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
            card.style.display = "block";
        } else {
            card.style.display = "none";
        }
    });
});
function deleteEvent(id){

if(!confirm("Delete this event?")) return;

fetch("/GEETHAYAANA/backend/deleteEvent.php?id=" + id)
.then(res => res.json())
.then(data => {

    alert("✅ Event Deleted Successfully");

    // 🔥 remove card instantly (no refresh)
    location.reload(); // OR remove card manually if you want advanced

})
.catch(err => {
    alert("❌ Error deleting event");
});
}

function toggleSidebar(){
  document.getElementById("sidebar").classList.toggle("active");
}

</script>

<footer class="footer">
<div class="container">
© 2026 GeethsConnect
</div>
</footer>

</body>
</html>