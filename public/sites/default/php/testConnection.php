<?php
$host = 'localhost';
$user = 'bobp';
$pass = 'Developer2025+';
$dbname = 'local_intro';

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully!";
