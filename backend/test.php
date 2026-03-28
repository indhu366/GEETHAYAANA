<?php
$conn = new mysqli("localhost", "root", "", "geethayana", 3307);

if ($conn->connect_error) {
    die("FAILED");
} else {
    echo "CONNECTED SUCCESS";
}
?>