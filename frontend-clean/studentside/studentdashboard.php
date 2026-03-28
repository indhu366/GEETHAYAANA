
<?php
session_start();
if(!isset($_SESSION['userEmail'])){
    header("Location: /GEETHAYAANA/frontend-clean/studentside/login.html");
    exit();
}
include "../../backend/db.php";

$today = date("Y-m-d");
$next2Days = date("Y-m-d", strtotime("+2 days"));

/* 🔴 LIVE EVENTS (TODAY) */
$liveEvents = $conn->query("
    SELECT * FROM events 
    WHERE date = '$today'
");

/* 🔥 UPCOMING (+2 DAYS) */
$upcoming = $conn->query("
    SELECT * FROM events 
    WHERE date > '$today' AND date <= '$next2Days'
");

/* ❗ IF NO UPCOMING → NEXT AVAILABLE */
if($upcoming->num_rows == 0){
    $upcoming = $conn->query("
        SELECT * FROM events 
        WHERE date > '$today'
        ORDER BY date ASC
        LIMIT 5
    ");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Student Dashboard </title>
<link rel="stylesheet" href="../common/css/style.css" />

<style>
@media(max-width:768px){
.row{
grid-template-columns: 1fr;
}
}

.row{
display:grid;
grid-template-columns: 1fr 1fr 1fr; /* 🔥 3 cards in one row */
gap:20px;
margin-bottom:25px;
}
.chart-card{
padding:25px;
height:400px; /* increase height */
}

#branchChart{
width:90% !important;
height:100% !important;
}

/* ✅ REMOVE EXTRA WIDTH ISSUE */
.container{
max-width:1200px !important;
}


.flip-container{
perspective:1000px;
}

.flip-card{
width:100%;
height:130px;
position:relative;
transform-style:preserve-3d;
transition: transform 1s;
}

.flip-card.flip{
transform: rotateY(180deg);
}

.front, .back{
position:absolute;
width:100%;
height:100%;
display:flex;
flex-direction:column;
align-items:center;
justify-content:center;
border-radius:12px;
color:#fff;
backface-visibility:hidden;
font-weight:600;
}

/* 🔴 LIVE SIDE */
.front{
background: linear-gradient(45deg,#ff4d4d,#ff0000);
}

/* 🔵 UPCOMING SIDE */
.back{
background: linear-gradient(45deg,#1e90ff,#3742fa);
transform: rotateY(180deg);
}

.front{
box-shadow:0 0 20px red;
}

.event-item{
background: rgba(255,255,255,0.15);
padding:8px 12px;
margin:5px 0;
border-radius:8px;
width:90%;
text-align:center;
}

.event-item strong{
display:block;
font-size:14px;
}

.event-item span{
font-size:12px;
opacity:0.8;
}

</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
<body>

<header class="navbar">
<div class="container nav-flex">
<h2 class="logo">GEETHAYAANA2026</h2>

<nav class="nav-links">
<a href="studentdashboard.php">Dashboard</a>
<a href="events.php">Events</a>
<a href="gallery.html">Gallery</a>

<a class="btn outline" href="/GEETHAYAANA/frontend-clean/index.html">Logout</a>
</nav>
</div>
</header>

<section class="container" style="padding:40px 0;">
<div class="card">
<h1 style="font-size:32px;margin-bottom:8px;">Welcome, Student 👋</h1>
<p style="color:var(--muted);">
Here you can view events and college activity gallery.
</p>
</div>
</section>

<section class="container">
<h2 style="margin-bottom:16px;">Quick Actions</h2>


<div class="row">

<a href="events.php" class="card" style="display:block;">
<h3>🎯 Explore Events</h3>
<p style="color:var(--muted);margin-top:8px;">
View all Events here.
</p>
<p style="margin-top:12px;color:var(--secondary);font-weight:700;">
Open →
</p>
</a>

<a href="gallery.html" class="card" style="display:block;">
<h3>🖼️ Gallery</h3>
<p style="color:var(--muted);margin-top:8px;">
See photos from past college activities.
</p>
<p style="margin-top:12px;color:var(--secondary);font-weight:700;">
Open →
</p>
</a>

<div class="card">
<h3>🔥 Events</h3>
<br>
<div class="flip-container">
<div class="flip-card" id="flipCard">

<!-- FRONT → LIVE -->
<div class="front">
<?php
if($liveEvents->num_rows > 0){
    while($e = $liveEvents->fetch_assoc()){
        echo "🔴 TODAY: {$e['title']}<br>{$e['time']}";
    }
}else{
    echo "No Live Events Today";
}
?>
</div>

<!-- BACK → UPCOMING -->
<div class="back">
<?php
if($upcoming->num_rows > 0){
    while($e = $upcoming->fetch_assoc()){

        $eventDate = $e['date'];
        $today = date("Y-m-d");
        $tomorrow = date("Y-m-d", strtotime("+1 day"));

        if($eventDate == $today){
            $label = "🔴 Today";
        } elseif($eventDate == $tomorrow){
            $label = "🟡 Tomorrow";
        } else {
            $label = "📅 " . date("d M", strtotime($eventDate));
        }

        echo "
        <div class='event-item'>
            <strong>{$e['title']}</strong>
            <span>$label • {$e['time']}</span>
        </div>
        ";
    }
}else{
    echo "No Upcoming Events";
}
?>
</div>

</div>

</div>
</div>

</div>



</div>
</div>
</div>
</section>
<br>

<section class="container">

<div class="card chart-card">
<h2>Registrations by Department</h2>
<canvas id="branchChart"></canvas>
</div>

</section>

<footer class="footer">
<div class="container">
© 2026 GEETHAYAANA | Student Dashboard
</div>
</footer>

<script>
const name = localStorage.getItem("studentName");
if (name) {
  document.querySelector("h1").innerText = "Welcome, " + name + " 👋";
}
async function loadBranchGraph(){

try{

const res = await fetch("/GEETHAYAANA/backend/departmentChart.php");
const data = await res.json();

const departments = ["ECE","CSE","CSE(AI&ML)","EEE","ISE","AI&DS"];

const values = departments.map(dep=>{
    const found = data.find(d => d.department === dep);
    return found ? found.total : 0;
});

const ctx = document.getElementById("branchChart");

new Chart(ctx,{
type:"bar",
data:{
labels:departments,
datasets:[{
data:values,
backgroundColor:[
"#87CEEB",
"#FF69B4",
"#FFB6C1",
"#FFD700",
"#808080",
"#800080"
],
borderRadius:8,
barThickness:50
}]
},
options:{
plugins:{legend:{display:false}},
responsive:true,
scales:{
y:{beginAtZero:true,ticks:{precision:0}}
}
}
});

}catch(err){
console.log(err);
}

}

loadBranchGraph();


setInterval(()=>{
document.getElementById("flipCard").classList.toggle("flip");
},5000);


</script>

</body>
</html>