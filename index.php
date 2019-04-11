<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
	<title>Sign In</title>
</head>
<body>
	<h1>Sign In</h1>

<?php
	require_once("login_db.php");
	echo "This is a sample PHP app. If this is showing, that means we correctly connected to the MYSQL database.";
?>
</body>
</html>

<?php
$conn->close();
?>