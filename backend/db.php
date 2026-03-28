<?php
session_start();
$conn = new mysqli("localhost", "root", "", "geethayana",3307);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>