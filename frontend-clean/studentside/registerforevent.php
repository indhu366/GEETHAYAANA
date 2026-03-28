<?php
$conn = new mysqli("localhost","root","","geethayana",3307);

if ($conn->connect_error) {
    die("Connection failed");
}

if (!isset($_GET['id'])) {
    die("No event selected");
}

$id = $_GET['id'];

$result = $conn->query("SELECT * FROM events WHERE id='$id'");
$event = $result->fetch_assoc();

/* COUNT REGISTERED */
if($event['participationType'] == "team"){
  $countRes = $conn->query("SELECT COUNT(DISTINCT teamId) as total FROM registrations WHERE eventId='".$event['id']."'");
}else{
  $countRes = $conn->query("SELECT COUNT(*) as total FROM registrations WHERE eventId='".$event['id']."'");
}

$countRow = $countRes->fetch_assoc();
$registered = $countRow['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Event Registration | GeethsConnect</title>
<link rel="stylesheet" href="../common/css/style.css">
</head>

<body>

<header class="navbar">
<div class="container nav-flex">
<h2 class="logo">GeethsConnect</h2>

<nav class="nav-links">
<a href="dashboard.html">Dashboard</a>
<a href="events.php">Events</a>
<a href="gallery.html">Gallery</a>
<a class="btn outline" href="index.html">Logout</a>
</nav>
</div>
</header>

<section class="container" style="padding:30px 0;">
<div class="card" style="padding:25px;">

<h1>🎯 Event Registration</h1>
<br>

<p>
<b>Registering for:</b> <?php echo $event['title']; ?>
</p>
<br>

<p style="color:orange;font-weight:600;">
<?php
if($event['participationType'] == "individual"){
    $remaining = $event['maxParticipants'] - $registered;
    echo "Individual Event | Seats Left: $remaining / ".$event['maxParticipants'];
}else{
    $remainingTeams = $event['maxTeams'] - $registered;
    echo "Team Event | Min: ".$event['minTeamSize'].
         " | Max: ".$event['teamSize'].
         " | Teams Left: $remainingTeams / ".$event['maxTeams'];
}
?>
</p>

<br>

<form action="/GEETHAYAANA/backend/register.php" method="POST" onsubmit="return validateForm()">

<input type="hidden" name="eventId" value="<?php echo $event['id']; ?>">
<input type="hidden" name="eventTitle" value="<?php echo $event['title']; ?>">
<input type="hidden" name="eventDate" value="<?php echo $event['date']; ?>">
<input type="hidden" name="eventTime" value="<?php echo $event['time']; ?>">
<input type="hidden" name="participationType" value="<?php echo $event['participationType']; ?>">

<label id="countLabel">Number of Participants</label>
<input type="number" name="count" id="participantCount" min="1" oninput="generateFields()" required>

<div id="participantFields"></div>
<br>

<button class="btn orange" type="submit">Register</button>

</form>

</div>
</section>

<footer class="footer">
<div class="container">
© 2026 GeethsConnect
</div>
</footer>

<script>

const participationType = "<?php echo $event['participationType']; ?>";
const minTeam = <?php echo (int)$event['minTeamSize']; ?>;
const maxTeam = <?php echo (int)$event['teamSize']; ?>;
const maxParticipants = <?php echo (int)$event['maxParticipants']; ?>;
const maxTeams = <?php echo (int)$event['maxTeams']; ?>;
const registered = <?php echo (int)$registered; ?>;

/* AUTO SET FOR INDIVIDUAL */
window.onload = function () {
  if (participationType === "individual") {
    document.getElementById("participantCount").value = 1;
    document.getElementById("participantCount").readOnly = true;
    document.getElementById("countLabel").style.display = "none";
    document.getElementById("participantCount").style.display = "none";
    generateFields();
  }
};

function generateFields(){

const container = document.getElementById("participantFields");
const count = parseInt(document.getElementById("participantCount").value);

if(!count || count <= 0){
  alert("❌ Enter valid number");
  return;
}

container.innerHTML = "";

/* INDIVIDUAL EVENT */
if(participationType === "individual"){

  if(registered >= maxParticipants){
    alert("❌ Event Full");
    return;
  }

  container.innerHTML = `
  <div style="margin-top:15px;">
    <h3>Participant</h3>

    <label>Name</label>
    <input type="text" name="name1" required>

    <label>USN</label>
    <input type="text" name="usn1"
     pattern="4GW[0-9]{2}[A-Za-z]{2}[0-9]{3}"
     title="Format: 4GW12CS123"
     required>

    <label>Phone</label>
    <input type="text" name="phone1" pattern="[0-9]{10}" maxlength="10" required>

    <label>Email</label>
    <input type="email" name="email1" required>

    <label>Department</label>
    <select name="dept1" required>
      <option value="">Select Department</option>
      <option>ECE</option>
      <option>CSE</option>
      <option>EEE</option>
      <option>CSE(AI&ML)</option>
      <option>ISE</option>
      <option>AI&DS</option>
    </select>
  </div>
  `;
  return;
}

/* TEAM VALIDATION */
if(registered >= maxTeams){
  alert("❌ Team slots full");
  return;
}

if(count < minTeam){
  alert("❌ Minimum team size is " + minTeam);
  return;
}

if(count > maxTeam){
  alert("❌ Maximum team size is " + maxTeam);
  return;
}

/* TEAM FIELDS */
for(let i=1;i<=count;i++){

let html = `<div style="margin-top:15px;">`;

if(i === 1){

html += `<h3>Team Leader</h3>

<label>Name</label>
<input type="text" name="name${i}" required>

<label>Department</label>
<select name="dept${i}" required>
<option value="">Select Department</option>
<option>ECE</option>
<option>CSE</option>
<option>CSE(AI&ML)</option>
<option>EEE</option>
<option>ISE</option>
<option>AI&DS</option>

<label for="semester">Semester</label>
    <select name="semester" id="semester" required>
        <option value="">Select Semester</option>
        <option value="1">Sem 1</option>
        <option value="2">Sem 2</option>
        <option value="3">Sem 3</option>
        <option value="4">Sem 4</option>
        <option value="5">Sem 5</option>
        <option value="6">Sem 6</option>
        <option value="7">Sem 7</option>
        <option value="8">Sem 8</option>

<label>USN</label>
<input type="text" name="usn${i}"
pattern="4GW[0-9]{2}[A-Za-z]{2}[0-9]{3}"
required>

<label>Phone</label>
<input type="text" name="phone${i}" pattern="[0-9]{10}" maxlength="10" required>

<label>Email</label>
<input type="email" name="email${i}" required>

</select>
`;

}else{

html += `<h3>Member ${i}</h3>

<label>Name</label>
<input type="text" name="name${i}" required>

<label>Department</label>
<select name="dept${i}" required>
<option value="">Select Department</option>
<option>ECE</option>
<option>CSE</option>
<option>EEE</option>
<option>CSE(AI&ML)</option>
<option>ISE</option>
<option>AI&DS</option>

<label>USN</label>
<input type="text" name="usn${i}"
pattern="4GW[0-9]{2}[A-Za-z]{2}[0-9]{3}"
required>


</select>
`;
}

html += `</div>`;
container.innerHTML += html;

}

}

/* FINAL VALIDATION */
function validateForm() {
  const container = document.getElementById("participantFields");

  if (container.innerHTML.trim() === "") {
    alert("❌ Please fill participant details");
    return false;
  }

  return true;
}

</script>

</body>
</html>