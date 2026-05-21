
<?php
session_start();

if(!isset($_SESSION['userEmail'])){
    header("Location: ../../frontend-clean/studentside/login.html");
    exit();
}
include("../../backend/db.php");

$userEmail = $_SESSION['userEmail'];

$userQuery = $conn->query("SELECT * FROM users WHERE email='$userEmail'");
$user = $userQuery->fetch_assoc();

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

padding: 12px;
display: flex;
flex-direction: column;
align-items: stretch;
justify-content: flex-start;
overflow-y: auto;
}

.front{
box-shadow:0 0 20px red;
}

.event-item{
background: rgba(255,255,255,0.15);
padding:10px 12px;
margin:8px 0;
border-radius:10px;
width:100%;
text-align:left;
box-sizing:border-box;
}

.event-item strong{
display:block;
font-size:14px;
}

.event-item span{
font-size:12px;
opacity:0.8;
}



@media(max-width:768px){

  /* Quick actions one by one */
  .row{
    grid-template-columns: 1fr !important;
    gap: 15px;
  }

  .card{
    width: 100%;
  }

  /* Navbar fix */
  .nav-flex{
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }

  .nav-links{
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
  }

  .logo{
    font-size: 26px;
  }

  /* Welcome text */
  h1{
    font-size: 26px !important;
  }

  /* Events card bigger */
  .flip-card{
    height: 280px;
  }

  .event-item{
    width: 100%;
    text-align: left;
  }

  .event-item strong{
    font-size: 15px;
  }

  .event-item span{
    font-size: 12px;
  }
}

/* TOPBAR */
.topbar{
  display:flex;
  justify-content:space-between;
  align-items:center;
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
<body>

<!-- TOPBAR -->
<div class="topbar">
  <span class="menu-btn" onclick="toggleSidebar()">☰</span>

  <h2 class="logo">GEETHAYAANA 2026</h2>

  <div class="profile" onclick="toggleProfile()">
    👤
    <div class="profile-dropdown" id="profileBox">
      <p><strong><?php echo $user['name']; ?></strong></p>
      <p><?php echo $user['email']; ?></p>

      <a href="updateprofile.php">Update Profile</a>
      <a href="/GEETHAYAANA/frontend-clean/studentside/login.html">Logout</a>
    </div>
  </div>
</div>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
  <a href="studentdashboard.php">🏠 Dashboard</a>
  <a href="events.php">🎯 Events</a>
  <a href="gallery.html">🖼️ Gallery</a>
  <a href="myevents.php">📅 My Events</a>
</div>

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
  <div class="container" style="
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    flex-wrap:wrap;
    gap:20px;
    padding:20px 0;
  ">

    <!-- LEFT -->
    <div style="text-align:left;">
      <strong>Faculty Support</strong><br>
      Mr. Mahadevappa N (System Analyst)<br>
      Dr. Sunita Adarsh Yadwad (Professor)<br>
      Dr. Vishwesh J (Associate Professor)<br>
      Dr. Rajath A N (Assistant Professor)
    </div>

    <!-- CENTER -->
    <div style="text-align:center; flex:1; min-width:200px;">
      © 2026 GEETHAYAANA
    </div>

    <!-- RIGHT -->
    <div style="text-align:right;">
      <strong>Developed BY</strong><br>
      R. Indu (9381627097)<br>
      G. Shalini Priya (8247697549)
    </div>

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

const departments = ["ECE","CSE","CSE(AI&ML)","EEE","ISE","AI&DS","MBA"];

const values = departments.map(dep=>{
    const found = data.find(d => d.department.trim().toUpperCase() === dep);
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
        "#800080",
        "#808000"
      ],
      borderRadius:8,
      barThickness: window.innerWidth < 768 ? 25 : 50
    }]
  },
  options:{
    plugins:{
      legend:{display:false}
    },
    responsive:true,
    maintainAspectRatio:false,
    layout:{
      padding:{
        top:10,
        bottom:10,
        left:10,
        right:10
      }
    },
    scales:{
      x:{
        ticks:{
          color:"#fff",
          font:{
            size: window.innerWidth < 768 ? 10 : 14
          },
          maxRotation:45,
          minRotation:45
        },
        grid:{
          display:false
        }
      },
      y:{
        beginAtZero:true,
        ticks:{
          precision:0,
          color:"#fff",
          font:{
            size: window.innerWidth < 768 ? 10 : 14
          }
        },
        grid:{
          color:"rgba(255,255,255,0.1)"
        }
      }
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

function toggleSidebar(){
  let sidebar = document.getElementById("sidebar");
  sidebar.style.left = (sidebar.style.left === "0px") ? "-220px" : "0px";
}

function toggleProfile(){
  let box = document.getElementById("profileBox");
  box.style.display = (box.style.display === "block") ? "none" : "block";
}

</script>

</body>
</html>