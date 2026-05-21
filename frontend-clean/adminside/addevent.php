<?php
session_start();


if(!isset($_SESSION['isSuperAdmin'])){
    die("❌ Access denied");
}

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

<div class="topbar">

  <!-- ☰ MENU -->
  <span class="menu-btn" onclick="toggleSidebar()">☰</span>

  <h2 class="logo-text">GEETHAYAANA 2026</h2>

</div>

<div id="sidebar" class="sidebar">
  <a href="admindashboard.php">🏠 Dashboard</a>
  <a href="addevent.php">➕ Add Event</a>
  <a href="uploadphotos.html">🖼️ Upload Photos</a>
  <a href="/GEETHAYAANA/frontend-clean/studentside/login.html">Logout</a>
</div>

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
<label>Event Name</label>

<select id="eventSelect" name="title" onchange="toggleOtherInput()" required>
  <option value="">Select Event</option>
  <option value="Collage">Collage</option>
  <option value="Anthyakshari(Hindi)">Anthyakshari(Hindi)</option>
  <option value="Eassy Writing(English)">Eassy Writing(English)</option>
  <option value="Coking Without fire">Cooking Without fire</option>
  <option value="Anthyakshari(Kannada)">Anthyakshari(English)</option>
  <option value="Essay Writting(Kannada)">Essay Writting(Kannada)</option>
  <option value="Pencil Sketch">Pencil Sketch</option>
  <option value="Ek Minute">Ek Minute</option>
    <option value="Rangoli">Rangoli</option>
  <option value="Other">Other</option>
</select>

<div id="otherEventDiv" style="display:none; margin-top:10px;">
  <input type="text" name="other_event_name" placeholder="Enter event name">
</div>

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

<select name="am_pm" required>
  <option value="">AM/PM</option>
  <option value="AM">AM</option>
  <option value="PM">PM</option>
</select>
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
<input type="number" name="maxParticipants" min="1" max="99" oninput="validateNumber(this)">
</div>

<!-- TEAM -->
<div id="teamDiv" style="display:none;">
<label>Min Team Size</label>
<input type="number" name="minTeamSize" min="1" max="99" oninput="validateNumber(this)">

<label>Max Team Size</label>
<input type="number" name="teamSize" min="1" max="99" oninput="validateNumber(this)">

<label>Max Number of Teams</label>
<input type="number" name="maxTeams" min="1" max="99" oninput="validateNumber(this)">
</div>

<div style="margin-top:16px">
<label>Description</label>
<textarea name="description"></textarea>
</div>

<label>WhatsApp Group Link</label>
<input type="url" name="whatsappLink" placeholder="Enter WhatsApp group link(optional)">

<div style="margin-top:16px">
<label>Upload Event Poster</label>
<input type="file" name="image" accept="image/*,.pdf" placeholder="Upload event poster(optional)">
</div>

<!-- FACULTY -->
<div style="margin-top:20px">
<label>No. of Faculty Coordinators</label>
<input type="number" id="facultyCount"  min="0" oninput="generateFacultyFields()" required >
</div>

<div id="facultyFields"></div>


<!-- STUDENTS -->
<div style="margin-top:20px">
<label>No. of Student Coordinators</label>
<input type="number" id="studentCount" min="0" oninput="generateStudentFields()" require>
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
  <input type="text" name="facultyPhone[]" pattern="[0-9]{10}" maxlength="10" required>

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


<label>Dept</label>
<input type="text" name="studentDept[]">

<label>Phone</label>
    <input type="text" name="studentPhone[]" pattern="[0-9]{10}" maxlength="10" required>
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

    const type = document.getElementById("ptype").value;

if(type === "individual"){
    let max = document.querySelector("[name='maxParticipants']").value;

    if(max <= 0){
        alert("❌ Max participants cannot be 0 or negative");
        return; // STOP FORM
    }
}

if(type === "team"){
    let max = document.querySelector("[name='maxTeams']").value;

    if(max <= 0){
        alert("❌ Max teams cannot be 0 or negative");
        return; // STOP FORM
    }
}

    const formData = new FormData(this);

    fetch("../../backend/addEvent.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {

        console.log(data); 

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

function toggleSidebar(){
  document.getElementById("sidebar").classList.toggle("active");
}

function toggleOtherInput(){
  const select = document.getElementById("eventSelect");
  const otherDiv = document.getElementById("otherEventDiv");

  if(select.value === "Other"){
    otherDiv.style.display = "block";
  } else {
    otherDiv.style.display = "none";
  }
}

function validateNumber(input){

    // allow only digits
    input.value = input.value.replace(/[^0-9]/g, '');

    // block 0
    if(input.value == "0"){
        alert("❌ Value must be greater than 0");
        input.value = "";
    }
}

</script>

</body>
</html>