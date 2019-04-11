<?php
session_start();
require_once("login_db.php");

$user_logged_in = False;
if(isset($_SESSION['logged_in_user']))
	$user_logged_in = True;

$showAll = True;
if($_POST['id'] == "true" ||
	$_POST['date'] == "true" ||
	$_POST['gradterm'] == "true" ||
	$_POST['last'] == "true" ||
	$_POST['first'] == "true" ||
	$_POST['major'] == "true" ||
	$_POST['level'] == "true" ||
	$_POST['degree'] == "true" ||
	$_POST['email'] == "true" ||
	$_POST['birthday'] == "true" ||
	$_POST['color'] == "true") {

	$showAll = False;
}

// Select columns to query
$cols = "";
if($_POST['id'] == "true" || $showAll)
	$cols .= " ID,";
if($_POST['date'] == "true" || $showAll)
	$cols .= " DATE,";

if($_POST['gradterm'] == "true" || $showAll)
	$cols .= " GRADTERM,";
if($_POST['last'] == "true" || $showAll)
	$cols .= " LAST,";
if($_POST['first'] == "true" || $showAll)
	$cols .= " FIRST,";
if($_POST['major'] == "true" || $showAll)
	$cols .= " MAJOR,";
if($_POST['level'] == "true" || $showAll)
	$cols .= " LEVEL,";
if($_POST['degree'] == "true" || $showAll)
	$cols .= " DEGREE,";
if($user_logged_in == True) {
	if($_POST['email'] == "true" || $showAll)
		$cols .= " EMAIL,";
	if($_POST['birthday'] == "true" || $showAll)
		$cols .= " BIRTHDAY,";
	if($_POST['color'] == "true" || $showAll)
		$cols .= " COLOR,";
}

// Remove last comma
$cols = substr($cols, 0, -1);

$query = "SELECT ".$cols." FROM mamorales15_db.cs_graduates";

// Filter out records
if(isset($_POST['filterColumn']) && isset($_POST['filter']) &&
	$_POST['filterColumn'] != "" && $_POST['filter'] != "") {
	$query .= " WHERE ".$_POST['filterColumn']." REGEXP '".$_POST['filter']."'";
}


// Sort results
if(isset($_POST['sortingDesc']) && $_POST['sortingDesc'] != "") {
	$query .= " ORDER BY ".$_POST['sortingDesc']." DESC";
} else if(isset($_POST['sortingAsc']) && $_POST['sortingAsc'] != "") {
	$query .= " ORDER BY ".$_POST['sortingAsc']." ASC";
}


// Limit results
if(isset($_POST['itemsPerPage'])) {
	$start = ($_POST['page'] - 1) * $_POST['itemsPerPage'];
	$query .= " LIMIT ".$start.",".$_POST['itemsPerPage'];
}

try {
	$result = mysqli_query($conn, $query);
} catch (Exception $e) {
	echo 'Caught exception: ',  $e->getMessage(), "\n";
	echo 'Query: ', $query, "\n";
}

?>

<table id="tableID">
	<tr>
<?php
if($_POST['id'] == "true" || $showAll)
	echo '<th>ID</th>';
if($_POST['date'] == "true" || $showAll)
	echo '<th>Date</th>';
if($_POST['gradterm'] == "true" || $showAll)
	echo '<th>Graduation Term</th>';
if($_POST['last'] == "true" || $showAll)
	echo '<th>Last Name</th>';
if($_POST['first'] == "true" || $showAll)
	echo '<th>First Name</th>';
if($_POST['major'] == "true" || $showAll)
	echo '<th>Major</th>';
if($_POST['level'] == "true" || $showAll)
	echo '<th>Level</th>';
if($_POST['degree'] == "true" || $showAll)
	echo '<th>Degree</th>';
if($user_logged_in == True) {
	if($_POST['email'] == "true" || $showAll)
		echo '<th>Email</th>';
	if($_POST['birthday'] == "true" || $showAll)
		echo '<th>Birthday</th>';
	if($_POST['color'] == "true" || $showAll)
		echo '<th>Color</th>';
}
?>
	</tr>

<?php

while($row = mysqli_fetch_array($result)) {
    echo "<tr>";
    if($_POST['id'] == "true" || $showAll)
		echo "<td>" . $row['ID'] . "</td>";
	if($_POST['date'] == "true" || $showAll)
		echo "<td>" . $row['DATE'] . "</td>";
	if($_POST['gradterm'] == "true" || $showAll)
		echo "<td>" . $row['GRADTERM'] . "</td>";
	if($_POST['last'] == "true" || $showAll)
		echo "<td>" . $row['LAST'] . "</td>";
	if($_POST['first'] == "true" || $showAll)
		echo "<td>" . $row['FIRST'] . "</td>";
	if($_POST['major'] == "true" || $showAll)
		echo "<td>" . $row['MAJOR'] . "</td>";
	if($_POST['level'] == "true" || $showAll)
		echo "<td>" . $row['LEVEL'] . "</td>";
	if($_POST['degree'] == "true" || $showAll)
		echo "<td>" . $row['DEGREE'] . "</td>";
	if($user_logged_in ==True) {
		if($_POST['email'] == "true" || $showAll)
			echo "<td>" . $row['EMAIL'] . "</td>";
		if($_POST['birthday'] == "true" || $showAll)
			echo "<td>" . $row['BIRTHDAY'] . "</td>";
		if($_POST['color'] == "true" || $showAll)
			echo "<td>" . $row['COLOR'] . "</td>";
	}
    echo "</tr>";
}

?>

</table>

<?php
$conn->close();
?>