<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
	<title>User Page</title>
</head>
<body>
	<h1>User Page</h1>

	<?php
	require_once("login_db.php");
	$id = $_GET['id'];		// Used when you clicked on a row from main page

	// Show accessible links
	$user_logged_in = False;
	$admin_logged_in = False;

	if(isset($_SESSION['logged_in_user']))
		$user_logged_in = True;
	if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1)
		$admin_logged_in = True;

	if($user_logged_in == False) {
		echo('Error: You must be logged in as a user to view this page.<br />');
		echo('<a href="index.php">Sign In</a><br />');
	}
	echo('<a href="mainpage.php">Main page</a><br />');
	if($admin_logged_in == True)
		echo('<a href="admin.php">Admin page</a><br />');

	// Show sign out button if already signed in
	if($user_logged_in == True) {
		echo('User logged in ' . $_SESSION['logged_in_user'] . '<br />');
		echo('
			<form action="index.php" method="POST">
			<input type="hidden" name="signout" value="True">
			<input type="submit" value="Sign out">
			</form>
			');
	}

	if($user_logged_in == True) {

		$query = "SELECT EMAIL, BIRTHDAY, COLOR FROM mamorales15_db.cs_graduates WHERE ID=".$id;
		try {
			$result = mysqli_query($conn, $query);
		} catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
			echo 'Query: ', $query, "\n";
		}

		$row = mysqli_fetch_array($result);		// Assume just one record
		echo "<h3>Profile Information</h3>";
		echo "<div>";
		echo "Email: ".$row['EMAIL']."<br />";
		echo "Birthday: ".$row['BIRTHDAY']."<br />";
		echo "Favorite Color: ".$row['COLOR']."<br />";
		echo "</div>";
	}

	$query = "SELECT cs_graduates_id FROM mamorales15_db.user WHERE username='".$_SESSION['logged_in_user']."'";
	try {
		$result = mysqli_query($conn, $query);
	} catch (Exception $e) {
		echo 'Caught exception: ',  $e->getMessage(), "\n";
		echo 'Query: ', $query, "\n";
	}
	$row = mysqli_fetch_array($result);		// Assume just one record
	$my_profile = $id == $row['cs_graduates_id'];		// Check if this user is logged in so they can edit their profile

	if($my_profile) {
		echo ('
			<h3>Edit My Profile</h3>
			<form action="update.php" method="POST">
			Email: 
			<input type="email" id="email" name="email">
			<br>
			DOB:
			<input type="date" id="dob" name="dob">
			<br>
			Favorite Color:
			<input type="text" id="color" name="color">
			<br><br>
			<input type="hidden" id="myID" name="myID" value="'.$id.'">
			<input type="submit" value="Submit">
			</form>

			');
	}
	?>
</body>
</html>

<?php
$conn->close();
?>