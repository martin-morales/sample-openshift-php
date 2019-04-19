<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
	<title>LAMP App</title>
	<link rel="stylesheet" href="styles/styles.css">
</head>
<body>
	<h1>LAMP App</h1>

	<?php
	require_once("login_db.php");
	// Add any new users before querying User table
	$name = $_POST['name'];
	if(isset($name)) {
		$query = 'INSERT INTO `sampledb`.`User` (`name`) VALUES ("'.$name.'");';

		if ($conn->query($query) === TRUE) {
			echo "User created successfully";
		} else {
			echo "Error: <br>" . $conn->error;
		}
	}

	$query = 'SELECT * FROM User';
	$result = $conn->query($query);

	if($result) {
		echo "<table>";
		while ($row = mysqli_fetch_array($result)) {
			echo "<tr>";
			echo "<td>".$row['user_id']."</td>"; 
			echo "<td>".$row['name']."</td>";
			echo "</tr>";
		}
		echo "</table>";
	} else {
		echo "Error: The User table might not have been created yet. Query failed: $query";
	}
	?>

	<h3>Add New User</h3>
	<form action="index.php" method="post">
		Name: <input type="text" id="name" name="name"><br />
		<input type = "submit" onclick="validateUserInfo()">
	</form>

	<script>
		function validateUserInfo() {
			var name = document.getElementById("name").value;
			if(name === "") {
				output += "Name must not be blank.\n";
			}

			if(output != "") {
				alert(output);
			}
		}
	</script>
</body>
</html>

<?php
$conn->close();
?>