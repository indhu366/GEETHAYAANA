<?php
session_start();
include("../../backend/db.php");

$email = $_SESSION['userEmail'];

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $name = $_POST['name'];
    $newEmail = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    $sem = $_POST['sem'];

    $conn->query("
        UPDATE users 
        SET name='$name', email='$newEmail', password='$password', phone='$phone', sem='$sem'
        WHERE email='$email'
    ");

    $_SESSION['userEmail'] = $newEmail;

    echo "<script>alert('Profile Updated'); window.location='studentdashboard.php';</script>";
}

$user = $conn->query("SELECT * FROM users WHERE email='$email'")->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
<title>Update Profile</title>

<style>
body{
  font-family:Arial;
  background:#0b1220;
  display:flex;
  justify-content:center;
  align-items:center;
  height:100vh;
}

.form-box{
  background:#111827;
  padding:25px;
  border-radius:12px;
  width:320px;
  color:#fff;
  box-shadow:0 0 20px rgba(0,0,0,0.5);
}

.form-box h2{
  text-align:center;
  margin-bottom:15px;
}

.form-box input{
  width:100%;
  padding:10px;
  margin:8px 0;
  border-radius:8px;
  border:none;
  outline:none;
}

.form-box button{
  width:100%;
  padding:10px;
  background:#f97316;
  border:none;
  border-radius:8px;
  color:#fff;
  cursor:pointer;
  margin-top:10px;
}

.form-box button:hover{
  background:#ea580c;
}

/* PROFILE IMAGE */
.profile-pic{
  text-align:center;
  margin-bottom:10px;
}

.profile-pic img{
  width:80px;
  height:80px;
  border-radius:50%;
  object-fit:cover;
}

</style>
</head>

<body>

<div class="form-box">

<h2>Update Profile</h2>

<div class="profile-pic">
  <img src="https://via.placeholder.com/80" id="preview">
  <input type="file" onchange="previewImage(event)">
</div>

<form method="POST">

<input type="text" name="name" value="<?php echo $user['name']; ?>" required>

<input type="email" name="email" value="<?php echo $user['email']; ?>" required>

<input type="password" name="password" value="<?php echo $user['password']; ?>" required>

<input type="text" name="phone" placeholder="Phone Number">

<input type="text" name="sem" placeholder="Semester">

<button type="submit">Update</button>

</form>

</div>

<script>
function previewImage(event){
  const reader = new FileReader();
  reader.onload = function(){
    document.getElementById("preview").src = reader.result;
  }
  reader.readAsDataURL(event.target.files[0]);
}
</script>

</body>
</html>