<?php
include("../../backend/db.php");

$eventId = $_GET['eventId'] ?? "";

if(!$eventId){
    die("❌ No event selected");
}

$eventRes = $conn->query("SELECT title FROM events WHERE id='$eventId'");
$event = $eventRes->fetch_assoc();
$eventDetails = $conn->query("SELECT participationType FROM events WHERE id='$eventId'");
$eventInfo = $eventDetails->fetch_assoc();
$isTeamEvent = ($eventInfo['participationType'] === 'team');

$result = $conn->query("SELECT * FROM registrations WHERE eventId='$eventId' ORDER BY teamId");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Event Participants | GeethsConnect</title>
<link rel="stylesheet" href="../common/css/style.css">


<style>
    
table{
width:100%;
border-collapse: collapse;
margin-top:20px;
}
th, td{
border:1px solid #ddd;
padding:10px;
text-align:center;
}
th{
background:hsl(246, 92%, 10%);
}
</style>

</head>

<body>

<header class="navbar">
<div class="container nav-flex">
<h2 class="logo">Geethayaana</h2>
<nav class="nav-links">
<a href="eventlist.php">Back</a>
</nav>
</div>
</header>

<section class="container" style="padding:30px 0;">

<div class="card">
<h1>👥 Participants</h1>

<p style="color:orange;font-weight:600;">
Total Participants: <?php echo $result->num_rows; ?>
</p>

<label>Enter extra columns (comma separated)</label>
<input type="text" id="extraColumns" placeholder="e.g Marks,Remarks,Score">

<button class="btn download" onclick="downloadExcel()">⬇ Download Excel</button>

<table id="participantsTable">
<thead>
<tr>
<?php if($isTeamEvent){ echo "<th>Team Name</th>"; } ?>
<th>Name</th>
<th>USN</th>
<th>Department</th>
<th>phone number</th>
<th>Role</th>
</tr>
</thead>

<tbody>

<?php

if(!$isTeamEvent){

    // ✅ INDIVIDUAL EVENT → row by row
    while($row = $result->fetch_assoc()){
        echo "<tr>
        <td>{$row['name']}</td>
        <td>{$row['usn']}</td>
        <td>{$row['department']}</td>
        <td>{$row['phone']}</td>
        <td>Individual</td>
        </tr>";
    }

} else {

    // ✅ TEAM EVENT → group data
    $teams = [];

    while($row = $result->fetch_assoc()){

        $key = !empty($row['teamId']) ? $row['teamId'] : $row['team_name'];

        if(!isset($teams[$key])){
            $teams[$key] = [
                "names" => [],
                "usns" => [],
                "depts" => [],
                "team_name" => $row['team_name'] ?? $row['teamId'],
                "phone" => "",
                "role" => "Team"
            ];
        }

        // ✅ take only leader phone (first non-empty)
        if(!empty($row['phone']) && empty($teams[$key]['phone'])){
            $teams[$key]['phone'] = $row['phone'];
        }

        $teams[$key]["names"][] = $row['name'];
        $teams[$key]["usns"][] = $row['usn'];
        $teams[$key]["depts"][] = $row['department'];
    }

    foreach($teams as $team){
        echo "<tr>
        <td>{$team['team_name']}</td>
        <td>".implode('<br>', $team['names'])."</td>
        <td>".implode('<br>', $team['usns'])."</td>
        <td>".implode('<br>', $team['depts'])."</td>
        <td>{$team['phone']}</td>
        <td>{$team['role']}</td>
        </tr>";
    }

}

?>

</tbody>
</table>

</div>

</section>
<script>

function downloadExcel(){

let extra = document.getElementById("extraColumns").value;

let eventId = "<?php echo $eventId; ?>";

window.location.href =
"/GEETHAYAANA/backend/exportExcel.php?eventId="
+ eventId + "&extra=" + encodeURIComponent(extra);
}

</script>

</body>
</html>