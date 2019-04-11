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

	$query = 'SELECT * FROM User';
	$result = $conn->query($query);
	?>

	<table>
		<tr>
			<?php
			while ($row = mysqli_fetch_array($result)) {
				echo "<td>".$row['user_id']."</td>"; 
				echo "<td>".$row['name']."</td>";
			}
			?>
		</tr>
	</table>

</body>
</html>

<?php
$conn->close();
?>