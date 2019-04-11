<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
	<title>Admin Page</title>
</head>
<body>
	<h1>Admin Page</h1>

<?php
	// Show accessible links
	$user_logged_in = False;
	$admin_logged_in = False;

	if(isset($_SESSION['logged_in_user']))
		$user_logged_in = True;
	if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1)
		$admin_logged_in = True;

	if($admin_logged_in == False) {
		echo('Error: You must be logged in as an admin to view this page.<br />');
		echo('<a href="index.php">Sign In</a><br />');
	}
	echo('<a href="mainpage.php">Main page</a><br />');
	if($user_logged_in == True) {
		echo('<a href="user.php">User page</a><br />');
		// Show log out button
		echo('User logged in ' . $_SESSION['logged_in_user'] . '<br />');
		echo('
		<form action="index.php" method="POST">
			<input type="hidden" name="signout" value="True">
			<input type="submit" value="Sign out">
		</form>
		');
	} 
	if($admin_logged_in == True) {
		require_once("login_db.php");

		// Check if query to create a new user was submitted
		$first_name = $_POST["first_name"];
		$last_name = $_POST["last_name"];
		$username = $_POST["username"];
		$password = $_POST["password"];
		$is_admin = ($_POST["admin"] === "on" ? 1 : 0);
		$id = $_POST["cs_id"];

		if(isset($first_name) && isset($last_name) &&
			isset($username) && isset($password)) {
			$global_salt = "^e*8ID1$19Y%";
			$salted_pwd = $global_salt . $username . $password;
			$hashed_pwd = hash("sha256", $salted_pwd);

			$query = 'INSERT INTO `mamorales15_db`.`user` (`first_name`, `last_name`, `username`, `password`, `salt`, `hash_alg`, `time_of_account_creation`, `time_of_last_login`, `is_administrator`, `cs_graduates_id`) VALUES ("'.$first_name.'", "'.$last_name.'", "'.$username.'", "'.$hashed_pwd.'", "'.$global_salt.'", "SHA256", NOW(), NOW(), "'.$is_admin.'", "'.$id.'");';

			if ($conn->query($query) === TRUE) {
				echo "User created successfully";
			} else {
				echo "Error: <br>" . $conn->error;
			}
		}

		echo('
			<!-- Form to create a new user -->
			<h3>Add New Users</h3>
			<form action="admin.php" method="post">
				First name: <input type="text" id="first_name" name="first_name"><br />
				Last name: <input type="text" id="last_name" name="last_name"><br />
				Username: <input type="text" id="username" name="username"><br />
				Password: <input type="password" id="password" name="password"><br />
				CS Graduate ID: <input type="text" id="cs_id" name="cs_id"><br />
				Grant Administrator privileges <input type="checkbox" name="admin"><br />
				<input type="submit" onclick="validateCredentials()">
			</form>

			<script>
				function validateCredentials() {
					var first_name = document.getElementById("first_name").value;
					var last_name = document.getElementById("last_name").value;
					var username = document.getElementById("username").value;
					var password = document.getElementById("password").value;
					var output = "";

					if(first_name === "") {
						output += "First name must not be blank.\n";
					}
					if(last_name === "") {
						output += "Last name must not be blank.\n";
					}
					if(username === "") {
						output += "Username must not be blank.\n";
					}
					if(password === "") {
						output += "Password must not be blank.\n";
					}

					if(output != "") {
						alert(output);
					}
				}
			</script>
		');

		if(isset($_POST['show_users']) && $_POST['show_users'] == True) {
			require_once("login_db.php");

			$query = 'SELECT * FROM `user`';
			$result = $conn->query($query);

			if ($result->num_rows > 0) {
				echo "<br />
					<table style='text-align: center'>
						<tr>
							<th>First Name</th>
							<th>Last Name</th>
							<th>Username</th>
							<th>Time of account creation</th>
							<th>Time of last login</th>
							<th>Is Administrator</th>
						</tr>";
				// output data of each row
				while($row = $result->fetch_assoc()) {
					echo "<tr>".
							"<td>".$row["first_name"]."</td>".
							"<td>".$row["last_name"]."</td>".
							"<td>".$row["username"]."</td>".
							"<td>".$row["time_of_account_creation"]."</td>".
							"<td>".$row["time_of_last_login"]."</td>".
							"<td>".$row["is_administrator"]."</td>".
						"</tr>";
				}
				echo "</table>";
			} else {
				echo "0 user results";
			}
		}
		echo('<br />
			<form action="admin.php" method="POST">
				<input type="hidden" name="show_users" value="True">
				<input type="submit" value="Show list of registered users">
			</form>
		');
	}	// Close if admin logged in = True block
?>
</body>
</html>

<?php
$conn->close();
?>