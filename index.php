<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
	<title>Sample PHP App</title>
</head>
<body>
	<h1>Sample PHP App</h1>

<?php
	require_once("login_db.php");
	echo "This is a sample PHP app. If this is showing, that means we correctly connected to the MYSQL database.";

	$query = 'SHOW DATABASES';

	$result = $conn->query($query);
	?>

	<table id="tableID">
		<tr>
			<?php
			while ($row = mysqli_fetch_array($result)) {
				echo "<td>".$row[0]."</td>"; 
			}
			?>
		</tr>
	</table>
?>
</body>
</html>

<?php
$conn->close();
?>