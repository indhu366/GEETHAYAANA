<?php
session_start();


if(!isset($_SESSION['userEmail'])){
    header("Location: login.html");
    exit();
}

include("../../backend/db.php");
$email = $_SESSION['userEmail'];

$sql = "
SELECT e.title, e.date, e.time
FROM registrations r
JOIN events e ON r.eventId = e.id
WHERE r.email = '$email'
";

$result = $conn->query($sql);


?>

<!DOCTYPE html>
<html>
<head>
<title>My Events</title>

<style>
body{
  background:#0b1220;
  font-family:Arial;
  color:#fff;
  margin:0;
}

/* HEADER */
.top{
  padding:15px;
  text-align:center;
  background:#111;
  font-size:20px;
  font-weight:bold;
}

/* CONTAINER */
.container{
  padding:20px;
}

/* EVENT CARD */
.event-card{
  background:#111827;
  padding:15px;
  border-radius:12px;
  margin-bottom:15px;
  box-shadow:0 0 10px rgba(0,0,0,0.5);
}

.event-card h3{
  margin:0;
  color:#f97316;
}

.event-card p{
  margin:5px 0;
  font-size:14px;
  color:#ccc;
}

/* NO DATA */
.no-data{
  text-align:center;
  margin-top:50px;
  color:#aaa;
}
</style>
</head>

<body>

<div class="top">📅 My Registered Events</div>

<div class="container">

<?php
if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        echo "
        <div class='event-card'>
            <h3>{$row['title']}</h3>
            <p>📅 {$row['date']}</p>
            <p>⏰ {$row['time']}</p>
        </div>
        ";
    }
}else{
    echo "<div class='no-data'>No events registered yet</div>";
}
?>

</div>

</body>
</html>