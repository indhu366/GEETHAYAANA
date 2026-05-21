<?php
session_start();
if(!isset($_SESSION['adminEmail'])){
    header("Location: /GEETHAYAANA/frontend-clean/studentside/login.html");
    exit();
}
$isSuperAdmin = $_SESSION['isSuperAdmin'] ?? false;
include("../../backend/db.php");

$email = $_SESSION['adminEmail'];

$res = $conn->query("SELECT * FROM users WHERE email='$email'");

if($res && $res->num_rows > 0){
    $admin = $res->fetch_assoc();
} else {
    $admin = ['name' => 'Admin', 'email' => $email];
}

$res1 = $conn->query("SELECT COUNT(*) as total FROM events");
$row1 = $res1->fetch_assoc();
$totalEvents = $row1['total'];

$res2 = $conn->query("SELECT COUNT(*) as total FROM registrations");
$row2 = $res2->fetch_assoc();
$totalStudents = $row2['total'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard | GEETHAYAANA</title>
<link rel="stylesheet" href="../common/css/style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

/* ✅ LOGO WITH IMAGE */

.profile-dropdown.show{
  display:block;
}

.topbar{
  position:relative;
}

.logo-box{
display: flex;
align-items: center;
gap: 10px;
}

.logo-img{
width: 50px;
height: 50px;
mix-blend-mode: multiply;
}

.logo-text{
  position:absolute;
  left:50%;
  transform:translateX(-50%);
  font-size:22px;
  font-weight:bold;
  color:#F97316;
}

/* ✅ 2 CARDS PER ROW */
.row{
display:grid;
grid-template-columns: 1fr 1fr;
gap:20px;
margin-bottom:25px;
}

/* ✅ CHART FULL WIDTH */
.chart-card{
padding:25px;
height:400px;
}

#branchChart{
width:90% !important;
height:100% !important;
}

/* ✅ FIX WIDTH */
.container{
max-width:1200px !important;
}

/* SIDEBAR DEFAULT */
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

/* ✅ WHEN ACTIVE */
.sidebar.active{
  left:0;
}

/* PROFILE DROPDOWN */
.profile-dropdown{
  display:none;
}

/* ✅ WHEN CLICKED */
.profile-dropdown.show{
  display:block;
}

/* PROFILE ICON POSITION */
.profile{
  position:absolute;
  right:20px;
  cursor:pointer;
  font-size:20px;
}

.menu-btn{
  font-size:24px;
  cursor:pointer;
  color:#fff;
  padding:10px;
}

/* Sidebar hidden */
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

/* When opened */
.sidebar.active{
  left:0;
}



</style>

</head>

<body>

<!-- Navbar -->
<div class="topbar">

  <!-- ☰ MENU -->
  <span class="menu-btn" onclick="toggleSidebar()">☰</span>

  <h2 class="logo-text">GEETHAYAANA 2026</h2>

  <!-- PROFILE -->
  <div class="profile" onclick="toggleProfile()">
    👤
    <div class="profile-dropdown" id="profileBox">
      <p><strong><?php echo $admin['name']; ?></strong></p>
      <p><?php echo $admin['email']; ?></p>
      

      
      <a href="/GEETHAYAANA/frontend-clean/studentside/login.html">Logout</a>
    </div>
  </div>

</div>

<div id="sidebar" class="sidebar">
  <a href="admindashboard.php">🏠 Dashboard</a>
  <a href="addevent.php">➕ Add Event</a>
  <a href="uploadphotos.html">🖼️ Upload Photos</a>
</div>


<section class="container" style="padding:30px 0;">
<div class="card">
<h1 style="font-size:32px;margin-bottom:8px;">Admin Dashboard 👨‍💻</h1>
<p style="color:var(--muted);">
Manage events and download participant data from here.
</p>
</div>
</section>

<?php if($isSuperAdmin == true){ ?>

<section class="container">
<div class="card" style="text-align:center; margin-bottom:20px;">

<h2>🔐 Portal Control</h2>

<form method="POST" action="../../backend/togglePortal.php">

    <button type="submit" name="action" value="open"
    style="padding:10px 20px; margin:10px; background:green; color:white; border:none; border-radius:6px;">
        🟢 Open Portal
    </button>

    <button type="submit" name="action" value="close"
    style="padding:10px 20px; margin:10px; background:red; color:white; border:none; border-radius:6px;">
        🔴 Close Portal
    </button>

</form>

</div>
</section>

<?php } ?>

<!-- ROW 1 -->
<section class="container">
<h2 style="margin-bottom:16px;">Quick Actions</h2>

<div class="row">

<a href="addevent.php" class="card">
<h3>➕ Add New Event</h3>
<p style="color:var(--muted);margin-top:8px;">
Create and publish a new college event.
</p>
<p style="margin-top:12px;color:var(--secondary);font-weight:700;">
Open →
</p>
</a>

<a href="uploadphotos.html" class="card">
<h3>🖼️ Upload Photos</h3>
<p style="color:var(--muted);margin-top:8px;">
Add event photos to student gallery.
</p>
<p style="margin-top:12px;color:var(--secondary);font-weight:700;">
Open →
</p>
</a>

</div>
</section>

<!-- ROW 2 -->
<section class="container">
<h2 style="margin-bottom:16px;">Overview</h2>

<div class="row">

<a href="eventlist.php" style="text-decoration:none;color:inherit;">
<div class="card">
<h3>🎯 Total Events</h3>

<p id="totalEvents"
style="font-size:34px;font-weight:800;margin-top:10px;color:var(--secondary);">
<?php echo $totalEvents; ?>
</p>

<p style="color:var(--muted);margin-top:6px;">
Events created so far
</p>
</div>
</a>

<div class="card">
<h3>👥 Registered Students</h3>

<p id="totalStudents"
style="font-size:34px;font-weight:800;margin-top:10px;color:var(--secondary);">
<?php echo $totalStudents; ?>
</p>

<p style="color:var(--muted);margin-top:6px;">
Students registered
</p>
</div>

</div>
</section>

<!-- ROW 3 -->
<section class="container">

<div class="card chart-card">
<h2>Registrations by Department</h2>
<canvas id="branchChart"></canvas>
</div>

</section>

<!-- Footer -->
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
      Dr. Rajath A N (Associate Professor)
  
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

/* ADMIN CHECK */


/* COUNTER */
function animateCounter(id, value){
let el = document.getElementById(id);
if(value === 0){ el.innerText = 0; return; }

let start = 0;
let counter = setInterval(()=>{
start++;
el.innerText = start;
if(start >= value) clearInterval(counter);
},20);
}



/* LOAD GRAPH */



async function loadBranchGraph(){

try{

const res = await fetch("/GEETHAYAANA/backend/departmentChart.php");
const data = await res.json();

console.log(data); // debug

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
"#87CEEB", // ECE
"#FF69B4", // CSE
"#FFB6C1", // CSE(AI&ML)
"#FFD700", // EEE
"#808080", // ISE
"#800080" , // AI&DS
"#808000"  // MBA
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



function toggleSidebar(){
  document.getElementById("sidebar").classList.toggle("active");
}

function toggleProfile(){
  document.getElementById("profileBox").classList.toggle("show");
}


</script>
</body>
</html>