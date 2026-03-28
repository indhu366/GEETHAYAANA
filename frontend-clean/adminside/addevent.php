<?php
session_start();

if(!isset($_SESSION['adminEmail'])){
    die("❌ Please login first");
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Event | GeethsConnect</title>
<link rel="stylesheet" href="../common/css/style.css">
</head>

<body>

<header class="navbar">
<div class="container nav-flex">
<h2 class="logo">GEETHAYAANA2026</h2>

<nav class="nav-links">
<a href="admindashboard.php">Dashboard</a>
<a href="addevent.php">Add Event</a>
<a href="uploadphotos.html">Upload Photos</a>
<a href="/GEETHAYAANA/frontend-clean/studentside/login.html">Logout</a>
</nav>
</div>
</header>

<section class="container" style="padding:30px 0;">
<div class="card">
<h1>➕ Add New Event</h1>
</div>
</section>

<section class="container">
<div class="card" style="padding:24px">

<form id="addEventForm" enctype="multipart/form-data">

<div class="grid" style="grid-template-columns:1fr 1fr;gap:16px">

<div>
<label>Event Title</label>
<input type="text" name="title" required>
</div>

<div>
<label>Event Type</label>
<select name="type" required>
<option value="">Select Type</option>
<option>Cultural</option>
<option>Sport</option>
<option>Technical</option>
<option>Workshop</option>
<option>Seminar</option>
<option>Hackathon</option>
</select>
</div>

<div>
<label>Date</label>
<input type="date" name="date" required>
</div>

<div>
<label>Time</label>
<input type="time" name="time" required>
</div>

</div>

<div class="grid" style="grid-template-columns:1fr 1fr;gap:16px">

<div>
<label>Venue</label>
<input type="text" name="venue" required>
</div>

<div>
<label>Status</label>
<select name="status" required>
<option>Open</option>
<option>Closed</option>
</select>
</div>

</div>

<!-- PARTICIPATION TYPE -->
<div style="margin-top:16px">
<label>Participation Type</label>

<select name="participationType" id="ptype" required>
<option value="">Select</option>
<option value="individual">Individual</option>
<option value="team">Team</option>
</select>
</div>

<!-- INDIVIDUAL -->
<div id="individualDiv" style="display:none;">
<label>Max Participants</label>
<input type="number" name="maxParticipants" min="1">
</div>

<!-- TEAM -->
<div id="teamDiv" style="display:none;">
<label>Min Team Size</label>
<input type="number" name="minTeamSize" min="1">

<label>Max Team Size</label>
<input type="number" name="teamSize" min="1">

<label>Max Number of Teams</label>
<input type="number" name="maxTeams" min="1">
</div>

<div style="margin-top:16px">
<label>Description</label>
<textarea name="description"></textarea>
</div>

<div style="margin-top:16px">
<label>Upload Event Poster</label>
<input type="file" name="eventFile">
</div>

<!-- FACULTY -->
<div style="margin-top:20px">
<label>No. of Faculty Coordinators</label>
<input type="number" id="facultyCount" min="0" oninput="generateFacultyFields()">
</div>

<div id="facultyFields"></div>


<!-- STUDENTS -->
<div style="margin-top:20px">
<label>No. of Student Coordinators</label>
<input type="number" id="studentCount" min="0" oninput="generateStudentFields()">
</div>

<div id="studentFields"></div>

<div style="margin-top:20px">
<button class="btn orange" type="submit">Publish Event</button>
<button class="btn outline" type="reset">Clear</button>
</div>

</form>
</div>
</section>

<footer class="footer">
<div class="container">
© 2026 GEETHAYAANA
</div>
</footer>

<script>

/* PARTICIPATION */
document.getElementById("ptype").addEventListener("change", function () {
const type = this.value;

document.getElementById("individualDiv").style.display =
(type === "individual") ? "block" : "none";


document.getElementById("teamDiv").style.display =
(type === "team") ? "block" : "none";
});

/* FACULTY */
function generateFacultyFields(){
const count = document.getElementById("facultyCount").value;
const container = document.getElementById("facultyFields");
container.innerHTML="";

for(let i=0;i<count;i++){
container.innerHTML += `
<div>
<label>Faculty ${i+1} Name</label>
<input type="text" name="facultyName[]" required>

<label>Phone</label>
<input type="text" name="facultyPhone[]" required>
</div>`;
}
}

/* STUDENTS */
function generateStudentFields(){
const count = document.getElementById("studentCount").value;
const container = document.getElementById("studentFields");
container.innerHTML="";

for(let i=1;i<=count;i++){
container.innerHTML += `
<div>
<label>Student ${i} Name</label>
<input type="text" name="studentName[]">

<label>USN</label>
<input type="text" name="studentUSN[]">

<label>Dept</label>
<input type="text" name="studentDept[]">

<label>Phone</label>
<input type="text" name="studentPhone[]">
</div>`;
}
}

/* PARTICIPATION */
document.getElementById("ptype").addEventListener("change", function () {
const type = this.value;

document.getElementById("individualDiv").style.display =
(type === "individual") ? "block" : "none";

document.getElementById("teamDiv").style.display =
(type === "team") ? "block" : "none";
});
document.getElementById("addEventForm").addEventListener("submit", function(e){

    e.preventDefault(); // ❌ stop redirect

    const formData = new FormData(this);

    fetch("/GEETHAYAANA/backend/addEvent.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {

        if(data.status === "success"){
            alert("✅ Event Published Successfully");

            // 🔥 CLEAR FORM
            document.getElementById("addEventForm").reset();

            // 🔥 HIDE dynamic sections also
            document.getElementById("individualDiv").style.display = "none";
            document.getElementById("teamDiv").style.display = "none";
            document.getElementById("facultyFields").innerHTML = "";
            document.getElementById("studentFields").innerHTML = "";
        }
        else{
            alert("❌ " + data.message);
        }

    })
  .catch(err => {
    console.log(err);
    alert("❌ Error adding event");
});  

});

</script>

</body>
</html>