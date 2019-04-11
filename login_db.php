<?php
$dbhost = getenv("MYSQL_SERVICE_HOST");
$dbuser = getenv("MYSQL_USER");
$dbpwd = getenv("MYSQL_PASSWORD");
$dbname = getenv("MYSQL_DATABASE");

// Create connection
$conn = new mysqli($dbhost, $dbuser, $dbpwd, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed. Please make sure you have the MYSQL_SERVICE_HOST, MYSQL_USER, MYSQL_PASSWORD, and MYSQL_DATABASE environment variables : " . $conn->connect_error);
}
?>