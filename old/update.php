<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
	<title>Update Profile Page</title>
</head>
<body>
	<h1>Update Profile</h1>

<?php
	// Show accessible links
	$user_logged_in = False;

	if(isset($_SESSION['logged_in_user'])){
		$user_logged_in = True;
		$user = $_SESSION['logged_in_user'] ;
	}
		

	if($user_logged_in == False) {
		echo('Error: You must be logged in as a user to view this page.<br />');
		echo('<a href="index.php">Sign In</a><br />');
	}
	if($user_logged_in = True){
		echo ('
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
			<input type="submit" value="Submit">
		</form>

		');

		echo('
		<br>
		<a href="mainpage.php">Main page</a><br/>
		<br>
		<form action="index.php" method="POST">
			<input type="hidden" name="signout" value="True">
			<input type="submit" value="Sign out">
		</form>
		');

		require_once("login_db.php");

		$email = $_POST["email"];
		$dob = $_POST["dob"];
		$color = $_POST["color"];

		$query = "SELECT cs_graduates_id FROM mamorales15_db.user WHERE username= '$user'";
		$result = mysqli_query($conn,$query);
		$row = mysqli_fetch_array($result);
		$id = $row['cs_graduates_id'];
				
		if(isset($email) && $email != "0" && $email != ""){
			$query = "UPDATE cs_graduates SET EMAIL = '$email' WHERE ID = '$id'";
			$result = mysqli_query($conn,$query);
		}
		if(isset($dob) && $dob != "0" && $dob != ""){
			$query = "UPDATE cs_graduates SET BIRTHDAY = '$dob' WHERE ID = '$id'";
			$result = mysqli_query($conn,$query);
		}
		if(isset($color) && $color != "0" && $color != ""){
			$query = "UPDATE cs_graduates SET COLOR ='$color' WHERE ID = '$id'";
			$result = mysqli_query($conn,$query);
		}
		
		if ($conn->query($query) === TRUE) {
			echo "<br>";
			echo "Profile updated successfully";
		} else {
			//echo "Error: <br>" . $conn->error;
		}
	}

// Redirect back to user.php with the GET id variable
header("Location: user.php?id=$id");
exit();

?>

