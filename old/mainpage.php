<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
	<title>Main Page</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<link type="text/css" rel="stylesheet" href="styles.css">
</head>
<body>
	<h1>Main Page</h1>

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
		<form action="mainpage.php" method="POST">
		<input type="hidden" name="signout" value="True">
		<input type="submit" value="Sign out">
		</form>');

	// Show sign in form if not already signed in
} else {
	echo('
		<form action="mainpage.php" method="post">
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

<form>
	<h3>Filter By</h3>
	Enter Regular Expression Here: <input type="text" id="filter"><br />
	<select id="filterColumn">
		<option value="">Select a column to filter by:</option>
		<option value="DATE">DATE</option>
		<option value="LAST">LAST NAME</option>
		<option value="FIRST">FIRST NAME</option>
		<option value="MAJOR">MAJOR</option>
		<option value="LEVEL">LEVEL</option>
		<option value="DEGREE">DEGREE</option>
		<?php
		if($user_logged_in == True) {
			echo '<option value="EMAIL">EMAIL</option>';
			echo '<option value="BIRTHDAY">BIRTHDAY</option>';
			echo '<option value="COLOR">COLOR</option>';
		}
		?>
	</select>

	<h3>Sort by Column</h3>
	<input type="radio" id="desc" name="sorting"> Descending Order<br />
	<input type="radio" id="asc" name="sorting"> Ascending Order<br />
	<select id="sortingColumn">
		<option value="">Select a column to sort by:</option>
		<option value="DATE">DATE</option>
		<option value="LAST">LAST NAME</option>
		<option value="FIRST">FIRST NAME</option>
		<option value="MAJOR">MAJOR</option>
		<option value="LEVEL">LEVEL</option>
		<option value="DEGREE">DEGREE</option>
		<?php
		if($user_logged_in == True) {
			echo '<option value="EMAIL">EMAIL</option>';
			echo '<option value="BIRTHDAY">BIRTHDAY</option>';
			echo '<option value="COLOR">COLOR</option>';
		}
		?>
	</select>

	<h3>Select Columns to Display</h3>
	<input type="checkbox" id="id">ID<br>
	<input type="checkbox" id="date">Date<br>
	<input type="checkbox" id="gradterm">Graduation Term<br>
	<input type="checkbox" id="last">Last Name<br>
	<input type="checkbox" id="first">First Name<br>
	<input type="checkbox" id="major">Major<br>
	<input type="checkbox" id="level">Level<br>
	<input type="checkbox" id="degree">Degree<br>
	<?php
	if($user_logged_in == True) {
		echo '<input type="checkbox" id="email">Email<br>';
		echo '<input type="checkbox" id="birthday">Birthday<br>';
		echo '<input type="checkbox" id="color">Color<br>';
	}
	?>

	<h3>Select number of records to display</h3>
	Page: <input type="text" id="page" value="1"><br />
	Items per page: <select id="itemsPerPage">
		<option value="10">10</option>
		<option value="50">50</option>
		<option value="100">100</option>
		<option value="250" selected="selected">250</option>
	</select><br />

	<button type="button" onclick="clearForm()">Clear</button>
	<button type="button" onclick="editQuery()">Submit</button>
	<br />
	<br />
</form>

<div id="myTable"></div>

<script>
	window.onload = function() {
		$.ajax({
			type: "POST",
			url: "queryGraduates.php",
			data: {
				showAll:true
			},
			success: function(html) {
				var elem = document.getElementById('myTable');
				elem.innerHTML = html;
					addRowHandlers();	// Allows you to see the profile of any student
				}
			});
	}

	function editQuery() {
			// Filter
			var filterColumn = document.getElementById('filterColumn').value;
			var filter = document.getElementById('filter').value;

			// Sort
			var sortingColumn = document.getElementById('sortingColumn').value;
			var sortingDesc = document.getElementById("desc").checked;
			var sortingAsc = document.getElementById("asc").checked;

			var sortingAscColumn = "";
			var sortingDescColumn = "";

			if(sortingAsc === true) {
				sortingAscColumn = sortingColumn;
			} else if(sortingDesc === true) {
				sortingDescColumn = sortingColumn;
			}

			// Select Columns
			var id = document.getElementById('id').checked;
			var date = document.getElementById('date').checked;
			var gradterm = document.getElementById('gradterm').checked;
			var last = document.getElementById('last').checked;
			var first = document.getElementById('first').checked;
			var major = document.getElementById('major').checked;
			var level = document.getElementById('level').checked;
			var degree = document.getElementById('degree').checked;
			<?php
			if($user_logged_in == True) {
				echo "var email = document.getElementById('email').checked;";
				echo "var birthday = document.getElementById('birthday').checked;";
				echo "var color = document.getElementById('color').checked;";
			}
			?>

			// Pages
			var page = document.getElementById('page').value;
			var itemsPerPage = document.getElementById('itemsPerPage').value;

			$.ajax({
				type: "POST",
				url: "queryGraduates.php",
				data: {
					filter:filter,
					filterColumn:filterColumn,
					sortingDesc:sortingDescColumn,
					sortingAsc:sortingAscColumn,
					id:id,
					date:date,
					gradterm:gradterm,
					last:last,
					first:first,
					major:major,
					level:level,
					degree:degree,
					<?php
					if($user_logged_in == True) {
						echo 'email:email,';
						echo 'birthday:birthday,';
						echo 'color:color,';
					}
					?>
					itemsPerPage:itemsPerPage,
					page:page,
				},
				success: function(html) {
					var elem = document.getElementById('myTable');
					elem.innerHTML = html;
					addRowHandlers();	// Allows you to see the profile of any student
				}
			});
		}

		function clearForm() {
			document.getElementById('filterColumn').value = "";
			document.getElementById('filter').value = "";

			// Sort
			document.getElementById('sortingColumn').value = "";
			document.getElementById("desc").checked = "false";
			document.getElementById("asc").checked = "false";

			// Select Columns
			document.getElementById('id').checked = "false";
			document.getElementById('date').checked = "false";
			document.getElementById('gradterm').checked = "false";
			document.getElementById('last').checked = "false";
			document.getElementById('first').checked = "false";
			document.getElementById('major').checked = "false";
			document.getElementById('level').checked = "false";
			document.getElementById('degree').checked = "false";
			document.getElementById('email').checked = "false";
			document.getElementById('birthday').checked = "false";
			document.getElementById('color').checked = "false";

			// Pagees
			document.getElementById('page').value = "1";
			document.getElementById('itemsPerPage').value ="250";
		}

		// Credit: https://stackoverflow.com/questions/1207939/adding-an-onclick-event-to-a-table-row
		function addRowHandlers() {
			var table = document.getElementById("tableID");
			var rows = table.getElementsByTagName("tr");
			for (i = 0; i < rows.length; i++) {
				var currentRow = table.rows[i];
				var createClickHandler = 
				function(row) 
				{
					return function() { 
						var cell = row.getElementsByTagName("td")[0];
						var id = cell.innerHTML;
						window.location.href = "user.php?id="+id;
					};
				};

				currentRow.onclick = createClickHandler(currentRow);
			}
		}

	</script>

</body>
</html>

<?php
$conn->close();
?>