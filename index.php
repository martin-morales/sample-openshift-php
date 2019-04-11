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

	// Check if sign out button was clicked
	if(isset($_POST['signout']) && $_POST['signout'] == "True") {
		echo("Signed out successfully<br />");
		session_unset();
		session_destroy();
	}

	// Try signing in if the form was submitted
	if(isset($_POST['username']) && isset($_POST['password'])) {
		$username = $_POST["username"];
		$password = $_POST["password"];

		$global_salt = "^e*8ID1$19Y%";
		$salted_pwd = $global_salt . $username . $password;
		$hashed_pwd = hash("SHA256", $salted_pwd);

		$query = 'SELECT username, is_administrator FROM `user` WHERE username="'.$username.'" AND password="'.$hashed_pwd.'"';

		$result = $conn->query($query);

		if($result->num_rows == 1) {
			echo "Successful login<br />";

			$row = $result->fetch_assoc();
			$_SESSION['logged_in_user'] = $username;
			$_SESSION['is_admin'] = $row['is_administrator'];

			// Update time of last login
			$query = 'UPDATE `user` SET time_of_last_login=NOW() WHERE username="'.$username.'"';
			if ($conn->query($query) === FALSE) {
				echo("Unsuccessful time_of_last_login update");
			}
		} else {
			echo("Unsuccessful login<br />");
		}
	}

	// Show accessible links
	$user_logged_in = False;
	$admin_logged_in = False;

	if(isset($_SESSION['logged_in_user']))
		$user_logged_in = True;
	if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1)
		$admin_logged_in = True;

	echo('<a href="mainpage.php">Main page</a><br />');
	if($user_logged_in == True) {
		$query = "SELECT cs_graduates_id FROM mamorales15_db.user WHERE username='".$_SESSION['logged_in_user']."'";
		try {
			$result = mysqli_query($conn, $query);
		} catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
			echo 'Query: ', $query, "\n";
		}
		$row = mysqli_fetch_array($result);		// Assume just one record
		$id = $row['cs_graduates_id'];		// Check if this user 

		echo('<a href="user.php?id='.$id.'">User page</a><br />');
	}
	if($admin_logged_in == True)
		echo('<a href="admin.php">Admin page</a><br />');

	// Show sign out button if already signed in
	if(isset($_SESSION['logged_in_user'])) {
		echo($_SESSION['logged_in_user']." is logged in.<br />");
		echo('
		<form action="index.php" method="POST">
			<input type="hidden" name="signout" value="True">
			<input type="submit" value="Sign out">
		</form>');

	// Show sign in form if not already signed in
	} else {
		echo('
			<form action="index.php" method="post">
				Username: <input type="text" id="username" name="username"><br />
				Password: <input type="password" id="password" name="password"><br />
				<input type="submit" onclick="validateCredentials()">
			</form>

			<script>
				function validateCredentials() {
					var username = document.getElementById("username").value;
					var password = document.getElementById("password").value;
					var output = "";

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
	}
?>
</body>
</html>

<?php
$conn->close();
?>