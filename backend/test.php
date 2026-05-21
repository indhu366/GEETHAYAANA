<?php
include("db.php");

if ($conn->connect_error) {
    die("FAILED");
} else {
    echo "CONNECTED SUCCESS";
}
?>